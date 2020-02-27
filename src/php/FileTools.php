<?php

namespace Frontastic\Apidocs;

class FileTools
{
    private $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    public function makeAbsolute(string $path): string
    {
        if ($path[0] !== '/') {
            $path = $this->rootPath . '/' . $path;
        }

        if (!file_exists($path)) {
            throw new \OutOfBoundsException("Connot find file $path");
        }

        return realpath($path);
    }

    public function getRelativePath(string $source, string $target): string
    {
        $absoluteTarget = array_values(array_filter(explode('/', $this->makeAbsolute($target))));
        $absoluteSource = array_values(array_filter(explode('/', $this->makeAbsolute($source))));

        if (!count($absoluteTarget) || !count($absoluteSource)) {
            throw new \OutOfBoundsException("$source and $target must exist for this method to work.");
        }

        $part = 0;
        while (isset($absoluteSource[$part]) &&
            isset($absoluteTarget[$part]) &&
            $absoluteSource[$part] === $absoluteTarget[$part]) {
            ++$part;
        }

        if ($part >= count($absoluteSource)) {
            return basename($source);
        }

        $relativeFromTargetToSource = array_merge(
            array_fill(0, count($absoluteTarget) - $part - 1, '..'),
            array_slice($absoluteSource, $part)
        );
        $relativeFromTargetToSource = implode('/', $relativeFromTargetToSource);
        return $relativeFromTargetToSource;
    }
}
