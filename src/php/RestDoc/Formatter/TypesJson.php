<?php

namespace Frontastic\Apidocs\RestDoc\Formatter;

use Frontastic\Apidocs\RestDoc\Formatter;

class TypedJson extends Formatter
{
    public function getContentType(): string
    {
        return 'application/json';
    }

    public function getSchema(string $type): object
    {
        return (object) [];
    }
}
