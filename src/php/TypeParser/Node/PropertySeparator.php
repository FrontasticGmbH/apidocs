<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class PropertySeparator extends Node
{
    public function __toString(): string
    {
        throw new \RuntimeException('Temporary non final token.');
    }
}
