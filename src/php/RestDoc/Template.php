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
            $this->renderType($type->type, $indentation, $skipIndent);
            return;
        }

        if (!$skipIndent) {
            echo str_repeat('  ', $indentation), '* ';
        }

        switch (true) {
            case $type instanceof Node\Optional:
                echo '*optional* ';
                $this->renderType($type->type, $indentation, true);
                break;
            case $type instanceof Node\Tuple:
                echo "tuple (array containing):\n\n";
                foreach ($type->types as $child) {
                    $this->renderType($child, $indentation + 1);
                    echo "\n\n";
                }
                break;
            case $type instanceof Node\Identifier:
                if (isset($this->classMap[$type->identifier])) {
                    $class = $this->classMap[$type->identifier];
                    echo '`', $class->name, '`', "\n\n";
                    foreach ($class->properties as $property) {
                        echo str_repeat('  ', $indentation + 1), '* `', $property->name, '`: ';
                        $this->renderType($property->type, $indentation + 1, true);
                    }
                } else {
                    echo '`', $type->identifier, '`', "\n\n";
                }
                break;
            case $type instanceof Node\Collection:
                echo 'collection of ';
                $this->renderType($type->type, $indentation, true);
                break;
            case $type instanceof Node\AnyOf:
                echo 'either of:', "\n\n";
                foreach ($type->types as $child) {
                    $this->renderType($child, $indentation + 1);
                    echo "\n\n";
                }
                break;
            case $type instanceof Node\Generic:
                if (isset($this->classMap[$type->identifier->identifier])) {
                    $class = $this->classMap[$type->identifier->identifier];
                    echo '`', $class->name, '`', "\n\n";

                    $properties = array_replace(
                        $class->properties,
                        $type->properties
                    );
                } else {
                    echo '`', $type->identifier->identifier, '` with:', "\n\n";
                    $properties = $type->properties;
                }

                foreach ($properties as $property) {
                    echo str_repeat('  ', $indentation + 1), '* `', ($property->identifier ?? $property->name), '` as ';
                    $this->renderType($property->type, $indentation + 1, true);
                }
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
