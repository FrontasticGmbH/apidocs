<?php

namespace Frontastic\Apidocs\RestDoc;

use Kore\DataObject\DataObject;

class Format extends DataObject
{
    public $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function parseTypes(TypeParser $parser)
    {
    }
}
