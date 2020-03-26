<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Optional extends Node
{
    /**
     * @var Node
     */
    public $type;

    public function __toString(): string
    {
        return '?' . $this->type;
    }
}
