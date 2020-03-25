<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\Tag;
use Frontastic\Apidocs\TypeParser;

class Return_ extends Tag
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
