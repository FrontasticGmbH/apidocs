<?php

namespace Frontastic\Apidocs\PhpDoc;

use Frontastic\Apidocs\EscapingTrait;
use Frontastic\Apidocs\FileTools;

class Template
{
    private $classIndex = [];

    private $classRegularExpression;

    private $externalClassIndex = [
        '\\Kore\\DataObject\\DataObject' => 'https://github.com/kore/DataObject',
        '\\Traversable' => 'https://www.php.net/manual/de/class.traversable.php',
        '\\Iterator' => 'https://www.php.net/manual/de/class.iterator.php',
        '\\IteratorAggregate' => 'https://www.php.net/manual/de/class.iteratoraggregate.php',
        '\\Throwable' => 'https://www.php.net/manual/de/class.throwable.php',
        '\\ArrayAccess' => 'https://www.php.net/manual/de/class.arrayaccess.php',
        '\\Serializable' => 'https://www.php.net/manual/de/class.serializable.php',
        '\\Closure' => 'https://www.php.net/manual/de/class.closure.php',
        '\\Generator' => 'https://www.php.net/manual/de/class.generator.php',
        '\\WeakReference' => 'https://www.php.net/manual/de/class.weakreference.php',
    ];

    private $externalClassRegularExpression;

    private $fileTools;

    use EscapingTrait;

    public function __construct(FileTools $fileTools)
    {
        $this->fileTools = $fileTools;
        $this->externalClassRegularExpression = '(`(\\?)?(' .
            implode(
                '|',
                array_map(
                    'preg_quote',
                    array_keys($this->externalClassIndex)
                )
            ) .
            ')(\\[\\])?`)';
    }

    public function linkOwn(string $from, string $input): string
    {
        $input = preg_replace_callback(
            $this->classRegularExpression,
            function (array $matches) use ($from): string {
                return sprintf(
                    '%s[`%s`](%s)%s',
                    $matches[1] ?? '',
                    substr(strrchr($matches[2], '\\'), 1),
                    $this->fileTools->getRelativePath($this->classIndex[$matches[2]], $from),
                    $matches[3] ?? ''
                );
            },
            $input
        );

        $input = str_replace(
            array_keys($this->classIndex),
            array_map(
                function (string $class): string {
                    return substr(strrchr($class, '\\'), 1);
                },
                array_keys($this->classIndex)
            ),
            $input
        );

        $input = preg_replace_callback(
            $this->externalClassRegularExpression,
            function (array $matches): string {
                return sprintf(
                    '%s[`%s`](%s)%s',
                    $matches[1] ?? '',
                    $matches[2],
                    $this->externalClassIndex[$matches[2]],
                    $matches[3] ?? ''
                );
            },
            $input
        );

        return $input;
    }

    public function addClassToIndex(string $className, string $file): void
    {
        $this->classIndex[$className] = $file;
        krsort($this->classIndex);

        $this->classRegularExpression = '(`([^`\s]*)(' .
            implode(
                '|',
                array_map(
                    'preg_quote',
                    array_keys($this->classIndex)
                )
            ) .
            ')([^`\s]*)`)';
    }

    public function render(
        string $targetFile,
        object $entity,
        array $methods,
        array $properties,
        string $relativeSourceLocation
    ) {
        ob_start();
        include(__DIR__ . '/../../templates/php.php');
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
