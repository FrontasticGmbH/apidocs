<?php

namespace Frontastic\Apidocs;

trait EscapingTrait
{
    public function e(string $text)
    {
        echo $text;
    }

    public function w(string $text)
    {
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

    public function removeNewLines(string $text): string
    {
        return preg_replace('([\r\n\s]+)', ' ', $text);
    }

    public function makeAnchor(string $heading)
    {
        echo trim(preg_replace('([^A-Za-z0-9__]+)', '-', strtolower($heading)), '-');
    }
}
