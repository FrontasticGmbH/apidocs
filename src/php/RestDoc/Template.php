<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\EscapingTrait;
use Frontastic\Apidocs\FileTools;
use Frontastic\Apidocs\TypeParser\Node;

class Template
{
    private $fileTools;

    private $classMap;

    use EscapingTrait;

    public function __construct(FileTools $fileTools, array $classMap)
    {
        $this->fileTools = $fileTools;
        $this->classMap = $classMap;
    }

    public function renderType(Node $type, $indentation = 0, $skipIndent = false): void
    {
        if ($type instanceof Node\Type) {
            $this->renderType($type->type);
            return;
        }

        if (!$skipIndent) {
            echo str_repeat('  ', $indentation), '* ';
        }

        switch (true) {
            case $type instanceof Node\Optional:
                echo '*optional* ';
                $this->renderType($type->type, $indentation + 1, true);
                break;
            case $type instanceof Node\Tuple:
                echo "tuple (array containing):\n\n";
                ++$indentation;
                foreach ($type->types as $child) {
                    echo str_repeat('  ', $indentation), '* ';
                    $this->renderType($child, $indentation + 1, true);
                    echo "\n\n";
                }
                --$indentation;
                break;
            case $type instanceof Node\Identifier:
                // @TODO: Link domain objects
                echo '`', $type->identifier, '`', "\n\n";
                break;
            case $type instanceof Node\Collection:
                echo 'collection of ';
                $this->renderType($type->type, $indentation + 1, true);
                break;
            case $type instanceof Node\Generic:
                // @TODO: Link domain objects
                echo '`', $type->identifier, '` with:', "\n\n";
                ++$indentation;
                foreach ($type->properties as $property) {
                    echo str_repeat('  ', $indentation), '* `', $property->identifier, '` as ';
                    $this->renderType($property->type, $indentation + 1, true);
                }
                --$indentation;
                break;
            default:
                throw new \OutOfBoundsException('Unhandled node: ' . get_class($type));
        }
    }

    public function render(
        string $targetFile,
        object $entity,
        array $paths,
        string $relativeSourceLocation
    ): void {
        ob_start();
        include(__DIR__ . '/../../templates/rest.php');
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
