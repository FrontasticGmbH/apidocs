<?php

namespace Frontastic\Apidocs;

use Kore\DataObject\DataObject;

abstract class Tag extends DataObject
{
    public function parseTypes(TypeParser $parser, ?string $fileName = null)
    {
        // Do nothing by default
    }
}
