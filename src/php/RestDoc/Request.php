<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\Tag;
use Frontastic\Apidocs\TypeParser;

class Request extends Tag
{
    public $method;

    public $url;

    public $bodyType;

    public function __construct(string $method, string $url, string $bodyType)
    {
        $this->method = $method;
        $this->url = $url;
        $this->bodyType = $bodyType;
    }

    public function parseTypes(TypeParser $parser, ?string $fileName = null)
    {
        $this->bodyType = $parser->parse($this->bodyType, $fileName);
    }
}
