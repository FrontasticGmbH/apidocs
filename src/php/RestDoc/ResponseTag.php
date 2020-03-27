<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\Tag;
use Frontastic\Apidocs\TypeParser;

class ResponseTag extends Tag
{
    public $status;

    public $bodyType;

    public $description;

    public function __construct(string $status, string $bodyType, ?string $description = null)
    {
        $this->status = (int) $status;
        $this->bodyType = $bodyType;
        $this->description = $description;
    }

    public function parseTypes(TypeParser $parser, ?string $fileName = null)
    {
        $this->bodyType = $parser->parse($this->bodyType, $fileName);
    }
}
