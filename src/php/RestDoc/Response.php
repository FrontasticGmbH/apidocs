<?php

namespace Frontastic\Apidocs\RestDoc;

use Kore\DataObject\DataObject;

class Response extends DataObject
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

    public function parseTypes(TypeParser $parser, string $fileName)
    {
        $this->bodyType = $parser->parse($this->bodyType, $fileName);
    }
}
