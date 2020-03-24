<?php

namespace Frontastic\Apidocs\RestDoc;

use Frontastic\Apidocs\EscapingTrait;
use Frontastic\Apidocs\FileTools;

class Template
{
    private $fileTools;

    use EscapingTrait;

    public function __construct(FileTools $fileTools)
    {
        $this->fileTools = $fileTools;
    }

    public function render(
        string $targetFile,
        object $entity,
        array $paths,
        string $relativeSourceLocation
    ) {
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
