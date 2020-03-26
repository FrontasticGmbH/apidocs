<?php

namespace Frontastic\Apidocs;

use \phpDocumentor\Reflection\TypeResolver;
use \phpDocumentor\Reflection\Types;

use Frontastic\Apidocs\PhpDoc;

class TypeParser
{
    private $typeResolver;
    private $contextFactory;
    private $tokenizer;
    private $parser;

    public function __construct()
    {
        $this->typeResolver = new TypeResolver();
        $this->contextFactory = new Types\ContextFactory();

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

        return $this->resolveTypes($typeAst, $context);
    }

    private function resolveTypes(TypeParser\Node $node, ?Types\Context $context = null): TypeParser\Node
    {
        if ($node instanceof TypeParser\Node\Identifier) {
            $node->identifier = (string) $this->typeResolver->resolve($node->identifier, $context);
        }

        if ($node instanceof TypeParser\Node\Generic) {
            $node->identifier->identifier = (string) $this->typeResolver->resolve(
                $node->identifier->identifier,
                $context
            );
        }

        if (isset($node->type)) {
            $this->resolveTypes($node->type, $context);
        }

        if (isset($node->types)) {
            foreach ($node->types as $type) {
                $this->resolveTypes($type, $context);
            }
        }

        if (isset($node->properties)) {
            foreach ($node->properties as $property) {
                $this->resolveTypes($property, $context);
            }
        }

        return $node;
    }
}
