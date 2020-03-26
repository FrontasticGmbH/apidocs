<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Type extends Node
{
    /**
     * @var Node
     */
    public $type;

    public function __toString(): string
    {
        return (string) $this->type;
    }
}
