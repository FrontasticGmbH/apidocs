<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Property extends Node
{
    /**
     * @var Identifier
     */
    public $identifier;

    /**
     * @var Node
     */
    public $type;

    public function __toString(): string
    {
        return $this->identifier . ': ' . $this->type;
    }
}
