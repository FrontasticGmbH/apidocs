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

use Frontastic\Apidocs\TypeParser\Node;

class RestDoc
{
    private $configurationFile;

    private $fileTools;

    private $typeParser;

    private $phpDoc;

    private $configuration;

    private $index = '';

    private $classMap = [];

    public function __construct(
        string $configurationFile,
        TypeParser $typeParser,
        PhpDoc $phpDoc,
        array $formatter = []
    ) {
        $this->configurationFile = realpath($configurationFile);
        $this->fileTools = new FileTools(dirname(($this->configurationFile)));
        $this->typeParser = $typeParser;
        $this->phpDoc = $phpDoc;

        $this->configuration = (object) Yaml::parse(file_get_contents($this->configurationFile));

        $this->formatter = array_merge(
            [
                'JSON' => new RestDoc\Formatter\TypedJson(),
                'TypedJSON' => new RestDoc\Formatter\TypedJson(),
            ],
            $formatter
        );

        $this->configuration->http = $this->configuration->http ?? [];
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
        $this->classMap = $this->phpDoc->getClasses();
        $template = new RestDoc\Template($this->fileTools, $this->classMap);

        include $this->configuration->autoloader;
        if (!file_exists($this->configuration->target)) {
            mkdir($this->configuration->target, 0755, true);
        }

        $this->index = '## HTTP API Documentation' . "\n\n" .
            'Download the [Swagger File](swagger.yml)' . "\n\n";

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

        $swagger = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => $this->configuration->name ?? 'Test Project',
            ],
            'paths' => [],
        ];

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

            $formatter = $this->getFormatter($entity);

            $paths = $this->getPaths($entity, $file->getPath());
            foreach ($paths as $path) {
                $responses = [];
                foreach ($path->responses as $response) {
                    $responses[(string) $response->status] = [
                        'description' => $response->description,
                        'content' => $this->visitTypeForSwagger($response->bodyType),
                    ];
                }

                $swagger['paths'][$path->request->url][$path->request->method] = [
                    'summary' => $path->summary,
                    'description' => $path->description,
                    'requestBody' => $this->visitTypeForSwagger($path->request->bodyType),
                    'responses' => $responses,
                ];
            }

            $template->render(
                $targetFile,
                $this->prepareEntity($entity),
                $paths,
                $this->fileTools->getRelativePath($file->getPath(), $targetFile)
            );
        }

        file_put_contents(
            $this->configuration->target . '/swagger.yml',
            Yaml::dump($swagger, 12, 2, Yaml::DUMP_OBJECT_AS_MAP)
        );
    }

    private function visitTypeForSwagger(Node $type): object
    {
        if ($type instanceof Node\Type) {
            return $this->visitTypeForSwagger($type->type);
        }

        switch (true) {
            case $type instanceof Node\Optional:
                $swaggerType = $this->visitTypeForSwagger($type->type);
                $swaggerType->nullable = true;
                return $swaggerType;
                break;
            case $type instanceof Node\Tuple:
                return (object) [
                    'type' => 'array',
                    'items' => array_map(
                        [$this, 'visitTypeForSwagger'],
                        $type->types
                    ),
                ];
            case $type instanceof Node\Identifier:
                if (isset($this->classMap[$type->identifier])) {
                    $swaggerType = (object) [
                        'type' => 'object',
                        'properties' => []
                    ];

                    $class = $this->classMap[$type->identifier];
                    foreach ($class->properties as $property) {
                        $swaggerType->properties[$property->name] = $this->visitTypeForSwagger($property->type);
                    }

                    return $swaggerType;
                } else {
                    return (object) ['type' => $type->identifier];
                }
            case $type instanceof Node\Collection:
                return (object) [
                    'type' => 'array',
                    'items' => $this->visitTypeForSwagger($type->type),
                ];
            case $type instanceof Node\AnyOf:
                return (object) [
                    'anyOf' => array_map(
                        [$this, 'visitTypeForSwagger'],
                        $type->types
                    ),
                ];
            case $type instanceof Node\Generic:
                $swaggerType = (object) [
                    'type' => 'object',
                    'properties' => []
                ];

                if (isset($this->classMap[$type->identifier->identifier])) {
                    $class = $this->classMap[$type->identifier->identifier];
                    $properties = array_replace(
                        $class->properties,
                        $type->properties
                    );
                } else {
                    $properties = $type->properties;
                }

                foreach ($properties as $property) {
                    $swaggerType->properties[(string) ($property->identifier ?? $property->name)] =
                        $this->visitTypeForSwagger($property->type);
                }
                return $swaggerType;
            default:
                throw new \OutOfBoundsException('Unhandled node: ' . get_class($type));
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

    private function getPaths(object $entity, string $fileName): array
    {
        return array_values(
            array_filter(
                array_map(
                    function (Method $method) use ($fileName): object {
                        $request = null;
                        $responses = [];

                        if ($method->getDocBlock() &&
                            ($tags = $method->getDocBlock()->getTags())) {
                            foreach ($tags as $tag) {
                                $tag = $this->createTag($tag, $fileName);

                                if ($tag && $tag instanceof RestDoc\RequestTag) {
                                    $request = $tag;
                                } elseif ($tag && $tag instanceof RestDoc\ResponseTag) {
                                    $responses[] = $tag;
                                }
                            }
                        }

                        return (object) [
                            'summary' => $method->getDocBlock() ? (string) $method->getDocBlock()->getSummary() : null,
                            'description' => $method->getDocBlock() ? (string) $method->getDocBlock()->getDescription() : null,
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

    private function createTag(BaseTag $tag, ?string $fileName = null): ?object
    {
        $tagName = preg_replace(
            '(^(?:Docs|Apidocs)\\\\)',
            __CLASS__ . '\\',
            $tag->getName() . 'Tag'
        );

        if (!class_exists($tagName)) {
            return null;
        }

        // Most amazing parser EVAR:
        $tag = new $tagName(...array_map('trim', preg_split('(["\']\\s*,\\s*["\'])', trim($tag->getDescription(), "()'\" \r\n\t"))));
        $tag->parseTypes($this->typeParser, $fileName);

        return $tag;
    }

    private function getFormatter(object $entity): RestDoc\Formatter
    {
        $format = 'JSON';
        if ($entity->getDocBlock() &&
            ($tags = $entity->getDocBlock()->getTags())) {
            foreach ($tags as $tag) {
                $tag = $this->createTag($tag, null);

                if ($tag instanceof RestDoc\Format) {
                    $format = $tag->format;
                    break;
                }
            }
        }

        if (!isset($this->formatter[$format])) {
            throw new \OutOfBoundsException('Unknown body formatter ' . $format);
        }

        return $this->formatter[$format];
    }
}
