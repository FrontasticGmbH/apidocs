<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Tuple extends Node
{
    /**
     * @var Node[]
     */
    public $types = [];

    public function __toString(): string
    {
        return '[' . implode(', ', $this->types) . ']';
    }
}
