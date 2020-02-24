<?php

namespace Frontastic\Apidocs;

class Template
{
    private $classIndex = [];

    private $fileTools;

    public function __construct(FileTools $fileTools)
    {
        $this->fileTools = $fileTools;
    }

    public function e(string $text) {
        echo $text;
    }

    public function w(string $text) {
        echo wordwrap(
            preg_replace(
                '((?<!' . PHP_EOL . ')' . PHP_EOL . '(?!\s*' . PHP_EOL . '))',
                ' ',
                preg_replace(
                    '(^\s+$)m',
                    '',
                    preg_replace(
                        '(\r\n|\r|\n)',
                        PHP_EOL,
                        $text
                    )
                )
            ),
            78
        );
    }

    public function makeAnchor(string $heading) {
        echo trim(preg_replace('([^A-Za-z0-9__]+)', '-', strtolower($heading)), '-');
    }

    public function linkOwn(string $from, string $input): string {
        foreach ($this->classIndex as $class => $docFile) {
            $input = preg_replace(
                '(`(\\?)?' . preg_quote($class) . '`)',
                '\\1[`' . substr(strrchr($class, '\\'), 1) . '`](' . $this->fileTools->getRelativePath($docFile, $from) . ')',
                $input
            );

            $input = str_replace(
                $class,
                substr(strrchr($class, '\\'), 1),
                $input
            );
        }

        return $input;
    }

    public function addClassToIndex(string $className, string $file): void
    {
        $this->classIndex[$className] = $file;
        krsort($this->classIndex);
    }

    public function render(string $targetFile, object $entity, array $methods, array $properties, string $relativeSourceLocation)
    {
        ob_start();
        include(__DIR__ . '/../templates/php.php');
        file_put_contents(
            $targetFile,
            preg_replace(
                "(\(\n    \n\))",
                "()",
                preg_replace(
                    "(\n{2,})",
                    "\n\n",
                    ob_get_clean()
                )
            )
        );
    }
}
