<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Generic extends Node
{
    /**
     * @var Identifier
     */
    public $identifier;

    /**
     * @var Property[]
     */
    public $properties = [];

    public function __toString(): string
    {
        $result = $this->identifier;
        if (count($this->properties)) {
            $result .= '{ ' . implode(', ', $this->properties) . ' }';
        }

        return $result;
    }
}
