<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\PhpDoc;

class TypeParser
{
    private $phpDocs;
    private $typeResolver;
    private $contextFactory;

    public function __construct(PhpDoc $phpDocs)
    {
        $this->phpDocs = $phpDocs;
        $this->typeResolver = new \phpDocumentor\Reflection\TypeResolver();
        $this->contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();
    }

    public function parse(string $type, string $fileName): object
    {
        if (!preg_match('(namespace\\s+(?P<namespace>[^\\s;]+)\\s*;)', file_get_contents($fileName), $matches)) {
            throw new \RuntimeException('Cannot determine namespace of file ' . $fileName);
        }
        $namespace = $matches['namespace'];

        $context = $this->contextFactory->createForNamespace($namespace, file_get_contents($fileName));

        $type = $this->typeResolver->resolve($type, $context);
        var_dump($type);

        return (object) [];
    }
}
