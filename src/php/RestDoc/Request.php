<?php

namespace Frontastic\Apidocs\RestDoc;

use Kore\DataObject\DataObject;

class Request extends DataObject
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

    public function parseTypes(TypeParser $parser, string $fileName)
    {
        $this->bodyType = $parser->parse($this->bodyType, $fileName);
    }
}
