<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\TypeParser\Node;

abstract class Formatter
{
    abstract public function getContentType(): string;

    abstract public function setClassMap(array $classMap): void;

    abstract public function getSchema(Node $type): array;
}
