<?php

namespace Frontastic\Apidocs\PhpDoc;

use Frontastic\Apidocs\Tag;
use Frontastic\Apidocs\TypeParser;

class ParamTag extends Tag
{
    public $name;
    public $type;

    public function __construct(string $value)
    {
        [$name, $type] = explode(' ', $value, 2);
        $this->name = $name;
        $this->type = $type;
    }

    public function parseTypes(TypeParser $parser, ?string $fileName = null)
    {
        $this->type = $parser->parse($this->type, $fileName);
    }
}
