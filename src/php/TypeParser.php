<?php

namespace Frontastic\Apidocs;

use Frontastic\Apidocs\PhpDoc;

class TypeParser
{
    private $typeResolver;
    private $contextFactory;
    private $tokenizer;
    private $parser;

    public function __construct()
    {
        $this->typeResolver = new \phpDocumentor\Reflection\TypeResolver();
        $this->contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();

        $this->tokenizer = new TypeParser\Tokenizer();
        $this->parser = new TypeParser\Parser();
    }

    public function parse(string $type, ?string $fileName = null): TypeParser\Node
    {
        $context = null;
        if ($fileName) {
            if (!preg_match('(namespace\\s+(?P<namespace>[^\\s;]+)\\s*;)', file_get_contents($fileName), $matches)) {
                throw new \RuntimeException('Cannot determine namespace of file ' . $fileName);
            }
            $namespace = $matches['namespace'];
            $context = $this->contextFactory->createForNamespace($namespace, file_get_contents($fileName));
        }

        try {
            $typeAst = $this->parser->parse(
                $this->tokenizer->tokenizeString($type)
            );
        } catch (\Exception $e) {
            throw new \RuntimeException(
                "Error parsing type $type in file $fileName: " . $e->getMessage(),
                503,
                $e
            );
        }

        // $type = $this->typeResolver->resolve($type, $context);
        return $typeAst;
    }
}
