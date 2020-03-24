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
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\File\LocalFile;

class RestDoc
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

        foreach ($this->configuration->http as $index => $fileName) {
            $this->configuration->http[$index] = $this->fileTools->makeAbsolute(
                ($this->configuration->source ?? '.') . '/' . $fileName
            );
        }
        $this->configuration->source = $this->fileTools->makeAbsolute($this->configuration->source);
        $this->configuration->target = $this->fileTools->makeAbsolute($this->configuration->target);
        $this->configuration->autoloader = $this->fileTools->makeAbsolute($this->configuration->autoloader);
    }

    public function render(): void
    {
        $template = new RestDoc\Template($this->fileTools);

        include $this->configuration->autoloader;
        if (!file_exists($this->configuration->target)) {
            mkdir($this->configuration->target, 0755, true);
        }

        $this->index = '## HTTP API Documentation' . "\n\n" .
            'Download the Swagger File' . "\n\n";

        try {
            $project = ProjectFactory::createInstance()->create(
                $this->configuration->name ?? 'Test Project',
                array_filter(
                    array_map(
                        function (string $fileName): ?LocalFile {
                            return new LocalFile($fileName);
                        },
                        $this->configuration->http
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
            touch($targetFile);

            $this->index .= sprintf(
                "* [%s](%s)\n",
                trim(str_replace($this->configuration->nameSpace, '', $entity->getFqsen()), '\\'),
                $this->fileTools->getRelativePath($targetFile, $this->configuration->target . '/README.md')
            );

            $template->render(
                $targetFile,
                $this->prepareEntity($entity),
                $this->getPaths($entity),
                $this->fileTools->getRelativePath($file->getPath(), $targetFile)
            );
        }
    }

    public function getIndex(): string
    {
        return $this->index . "\n";
    }

    public function getConfiguration(): object
    {
        return $this->configuration;
    }

    private function getTargetFileName(string $sourceFile): string
    {
        return preg_replace(
            '(\\.[a-zA-Z0-9.]+$)',
            '.rest.md',
            $this->configuration->target . str_replace($this->configuration->source, '', $sourceFile)
        );
    }

    private function getPaths(object $entity): array
    {
        return array_values(
            array_filter(
                array_map(
                    function (Method $method): object {
                        $request = null;
                        $responses = [];

                        if ($method->getDocBlock() &&
                            ($tags = $method->getDocBlock()->getTags())) {
                            foreach ($tags as $tag) {
                                $tag = $this->createTag($tag);
                                var_dump($tag);

                                if ($tag && $tag instanceof RestDoc\Request) {
                                    $request = $tag;
                                } elseif ($tag && $tag instanceof RestDoc\Response) {
                                    $responses[] = $tag;
                                }
                            }
                        }

                        return (object) [
                            'summary' => $method->getDocBlock() ? $method->getDocBlock()->getSummary() : null,
                            'description' => $method->getDocBlock() ? $method->getDocBlock()->getDescription() : null,
                            'request' => $request,
                            'responses' => $responses,
                        ];
                    },
                    $entity->getMethods()
                ),
                function (object $method) {
                    return (bool) $method->request;
                }
            )
        );
    }

    private function prepareEntity(object $entity): object
    {
        $isInterface = $entity instanceof \phpDocumentor\Reflection\Php\Interface_;

        return (object) [
            'summary' => $entity->getDocBlock() ? $entity->getDocBlock()->getSummary() : '',
            'description' => $entity->getDocBlock() ? $entity->getDocBlock()->getDescription() : '',
        ];
    }

    private function createTag(BaseTag $tag): ?object
    {
        $tagName = preg_replace(
            '(^(?:Docs|Apidocs)\\\\)',
            __CLASS__ . '\\',
            $tag->getName()
        );

        if (!class_exists($tagName)) {
            return null;
        }

        // Most amazing parser EVAR:
        return new $tagName(...array_map('trim', preg_split('(\\s*,\\s*)', trim($tag->getDescription(), '()'))));
    }
}
