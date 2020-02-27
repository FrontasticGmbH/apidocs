<?php

namespace Frontastic\Apidocs;

use Symfony\Component\Yaml\Yaml;

use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\File\LocalFile;

class PhpDoc
{
    private $configurationFile;

    private $fileTools;

    private $configuration;

    private $index = '';

    public function __construct(string $configurationFile)
    {
        $this->configurationFile = realpath($configurationFile);
        $this->fileTools = new FileTools(dirname(($this->configurationFile)));

        $this->configuration = (object) Yaml::parse(file_get_contents($this->configurationFile));

        foreach ($this->configuration->files as $index => $fileName) {
            $this->configuration->files[$index] = $this->fileTools->makeAbsolute(
                ($this->configuration->source ?? '.') . '/' . $fileName
            );
        }
        $this->configuration->source = $this->fileTools->makeAbsolute($this->configuration->source);
        $this->configuration->target = $this->fileTools->makeAbsolute($this->configuration->target);
        $this->configuration->autoloader = $this->fileTools->makeAbsolute($this->configuration->autoloader);
    }

    public function render(): void
    {
        $template = new Template($this->fileTools);

        include $this->configuration->autoloader;
        if (!file_exists($this->configuration->target)) {
            mkdir($this->configuration->target, 0755, true);
        }

        $this->index = '# ' . $this->configuration->name . "\n\n" .
            wordwrap('Here you find the API documentation for the relevant classes:', 78) . "\n\n";

        try {
            $project = ProjectFactory::createInstance()->create(
                $this->configuration->name ?? 'Test Project',
                array_filter(
                    array_map(
                        function (string $fileName): ?LocalFile {
                            return new LocalFile($fileName);
                        },
                        $this->configuration->files
                    )
                )
            );
        } catch (\Throwable $e) {
            // @TODO: There must be saner way to get information about the file
            // which could not be parsed, but not finding it…
            $fileName = null;
            foreach ($e->getTrace() as $call) {
                if (($call['class'] === 'phpDocumentor\\Reflection\\Php\\Factory\\AbstractFactory') &&
                    ($call['function'] === 'create') &&
                    count($call['args']) &&
                    ($call['args'][0] instanceof LocalFile)) {
                    $fileName = $call['args'][0]->path();
                }
            }

            echo '[e] Error parsing ', ($fileName ?: 'unknown'), ': ', $e->getMessage(), PHP_EOL;
            exit(2);
        }

        foreach ($project->getFiles() as $file) {
            $entity = array_values($file->getClasses())[0] ?? array_values($file->getInterfaces())[0] ?? null;
            if (!$entity) {
                continue;
            }

            $targetFile = $this->getTargetFileName($file->getPath());

            if (!file_exists(dirname($targetFile))) {
                mkdir(dirname($targetFile), 0755, true);
            }
            touch($targetFile);

            $template->addClassToIndex((string) $entity->getFqsen(), $targetFile);
        }

        foreach ($project->getFiles() as $file) {
            $entity = array_values($file->getClasses())[0] ?? array_values($file->getInterfaces())[0] ?? null;
            if (!$entity) {
                continue;
            }

            $targetFile = $this->getTargetFileName($file->getPath());

            $this->index .= sprintf(
                "* [%s](%s)\n",
                trim(str_replace($this->configuration->nameSpace, '', $entity->getFqsen()), '\\'),
                $this->fileTools->getRelativePath($targetFile, $this->configuration->target . '/README.md')
            );

            $template->render(
                $targetFile,
                $this->prepareEntity($entity),
                $this->getMethods($entity),
                $this->getProperties($entity),
                $this->fileTools->getRelativePath($file->getPath(), $targetFile)
            );
        }
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getConfiguration(): object
    {
        return $this->configuration;
    }

    private function getTargetFileName(string $sourceFile): string
    {
        return preg_replace(
            '(\\.[a-zA-Z0-9.]+$)',
            '.md',
            $this->configuration->target . str_replace($this->configuration->source, '', $sourceFile)
        );
    }

    private function getProperties(object $entity): array
    {
        return array_values(
            array_map(
                // Merge type information with information from doc block
                function (Property $property): object {
                    if (!count($property->getTypes()) && $property->getDocBlock()) {
                        foreach ($property->getDocBlock()->getTags() as $tag) {
                            if ($tag instanceof Var_) {
                                $property->addType((string) $tag->getType());
                            }
                        }
                    }

                    return (object) [
                        'name' => $property->getName(),
                        'isStatic' => $property->isStatic(),
                        'types' => $property->getTypes(),
                        'default' => $property->getDefault(),
                        'summary' => $property->getDocBlock() ? $property->getDocBlock()->getSummary() : '',
                    ];
                },
                // Only show public properties
                array_filter(
                    $entity instanceof Interface_ ? [] : $entity->getProperties(),
                    function (Property $property): bool {
                        return $property->getVisibility() == 'public';
                    }
                )
            )
        );
    }

    private function getMethods(object $entity): array
    {
        return array_values(
            array_map(
                function (Method $method): object {
                    $arguments = array_map(
                        function (Argument $argument) use ($method): object {
                            $description = '';
                            if ($method->getDocBlock()) {
                                foreach ($method->getDocBlock()->getTags() as $tag) {
                                    if ($tag instanceof Param &&
                                        $tag->getVariableName() === $argument->getName()) {
                                        $description = $tag->getDescription();
                                    }
                                }
                            }
                            return (object) [
                                'name' => $argument->getName(),
                                'type' => $argument->getType(),
                                'default' => $argument->getDefault(),
                                'isByReference' => $argument->isByReference(),
                                'isVariadic' => $argument->isVariadic(),
                                'description' => $description,
                            ];
                        },
                        $method->getArguments()
                    );

                    return (object) [
                        'name' => $method->getName(),
                        'summary' => $method->getDocBlock() ? $method->getDocBlock()->getSummary() : null,
                        'description' => $method->getDocBlock() ? $method->getDocBlock()->getDescription() : null,
                        'arguments' => $arguments,
                        'signature' => (
                            ($method->isStatic() ? 'static ' : '') .
                            ($method->isAbstract() ? 'abstract ' : '') .
                            'public function ' . $method->getName() . "(\n    " .
                            implode(
                                ",\n    ",
                                array_map(
                                    function (object $argument): string {
                                        return (
                                            ($argument->type ? $argument->type . ' ' : '') .
                                            ($argument->isByReference ? '&' : '') .
                                            ($argument->isVariadic ? '…' : '') .
                                            '$' . $argument->name .
                                            ($argument->default ? ' = ' . $argument->default : '')
                                        );
                                    },
                                    $arguments
                                )
                            ) .
                            "\n): " . $method->getReturnType()
                        ),
                        'return' => (string) $method->getReturnType(),
                    ];
                },
                array_filter(
                    $entity->getMethods(),
                    function (Method $method): bool {
                        return $method->getVisibility() == 'public';
                    }
                )
            )
        );
    }

    private function prepareEntity(object $entity): object
    {
        $isInterface = $entity instanceof \phpDocumentor\Reflection\Php\Interface_;

        return (object) [
            'isInterface' => $isInterface,
            'isAbstract' => !$isInterface && $entity->isAbstract(),
            'isFinal' => !$isInterface && $entity->isFinal(),
            'extends' => !$isInterface ? ((string) $entity->getParent()) : null,
            'implements' => ($isInterface ?
                array_map('strval', $entity->getParents()) :
                array_map('strval', $entity->getInterfaces())) ?: [],
            'name' => $entity->getName(),
            'fullName' => $entity->getFqsen(),
            'description' => $entity->getDocBlock() ? $entity->getDocBlock()->getDescription() : '',
        ];
    }
}
