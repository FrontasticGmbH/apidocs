<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Identifier extends Node
{
    /**
     * @var string
     */
    public $identifier;

    public function __toString(): string
    {
        return (string) $this->identifier;
    }
}
