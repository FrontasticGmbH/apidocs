<?php

namespace Frontastic\Apidocs\PhpDoc;

use Frontastic\Apidocs\Tag;
use Frontastic\Apidocs\TypeParser;

class ReturnTag extends Tag
{
    public $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function parseTypes(TypeParser $parser, ?string $fileName = null)
    {
        $this->type = $parser->parse($this->type, $fileName);
    }
}
