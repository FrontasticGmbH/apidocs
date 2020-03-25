<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\Tag;
use Frontastic\Apidocs\TypeParser;

class Format extends Tag
{
    public $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }
}
