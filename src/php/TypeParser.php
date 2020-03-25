<?php

namespace Frontastic\Apidocs;

use Frontastic\Apidocs\PhpDoc;

class TypeParser
{
    private $typeResolver;
    private $contextFactory;

    public function __construct()
    {
        $this->typeResolver = new \phpDocumentor\Reflection\TypeResolver();
        $this->contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();
    }

    public function parse(string $type, ?string $fileName = null): object
    {
        $context = null;
        if ($fileName) {
            if (!preg_match('(namespace\\s+(?P<namespace>[^\\s;]+)\\s*;)', file_get_contents($fileName), $matches)) {
                throw new \RuntimeException('Cannot determine namespace of file ' . $fileName);
            }
            $namespace = $matches['namespace'];

            $context = $this->contextFactory->createForNamespace($namespace, file_get_contents($fileName));
        }

        $type = $this->typeResolver->resolve($type, $context);

        return $type;
    }
}
