<?php

namespace Frontastic\Apidocs\TypeParser;

use Kore\DataObject\DataObject;

abstract class Node extends DataObject
{
    /**
     * @var object
     */
    public $token;

    abstract public function __toString(): string;
}
