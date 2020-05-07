<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Map extends Node
{
    /**
     * @var Node
     */
    public $key;

    /**
     * @var Node
     */
    public $value;

    public function __toString(): string
    {
        return 'array<' . $this->key . ', ' . $this->value . '>';
    }
}
