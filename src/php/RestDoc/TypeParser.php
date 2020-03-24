<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\PhpDoc;

class TypeParser
{
    private $phpDocs;

    public function __construct(PhpDoc $phpDocs)
    {
        $this->phpDocs = $phpDocs;
    }

    public function parse(string $type): object
    {
        // @TODO: https://github.com/phpDocumentor/TypeResolver
        var_dump($type);
        return (object) [];
    }
}
