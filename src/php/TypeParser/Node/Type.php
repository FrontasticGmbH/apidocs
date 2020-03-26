<?php

namespace Frontastic\Apidocs\TypeParser\Node;

use Frontastic\Apidocs\TypeParser\Node;

class Type extends Node
{
    /**
     * @var Node
     */
    public $content;

    public function __toString(): string
    {
        return (string) $this->content;
    }
}
