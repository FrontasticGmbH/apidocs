<?php

namespace Frontastic\Apidocs\RestDoc;

abstract class Formatter
{
    abstract public function getContentType(): string;

    abstract public function getSchema(string $type): object;
}
