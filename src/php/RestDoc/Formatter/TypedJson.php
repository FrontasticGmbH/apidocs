<?php

namespace Frontastic\Apidocs\RestDoc\Formatter;

use Frontastic\Apidocs\RestDoc\Formatter;
use Frontastic\Apidocs\TypeParser\Node;

class TypedJson extends Formatter
{
    /**
     * @var array
     */
    private $classMap = [];

    /**
     * @var mixed
     */
    private $swaggerTypeMap = [
        'int' => 'integer',
        'float' => 'number',
        'bool' => 'boolean',
    ];

    public function getContentType(): string
    {
        return 'application/json';
    }

    public function setClassMap(array $classMap): void
    {
        $this->classMap = $classMap;
    }

    public function getSchema(Node $type, bool $required = false): array
    {
        $schema = [
            $this->getContentType() => [
                'schema' => $this->visitTypeForSwagger($type),
            ],
        ];

        return $schema;
    }

    private function visitTypeForSwagger(Node $type): object
    {
        if ($type instanceof Node\Type) {
            return $this->visitTypeForSwagger($type->type);
        }

        switch (true) {
            case $type instanceof Node\Optional:
                $swaggerType = $this->visitTypeForSwagger($type->type);
                $swaggerType->nullable = true;
                return $swaggerType;
                break;
            case $type instanceof Node\Tuple:
                // There is no proper way to mdel tuples in swagger. The only
                // way for us to model them is considering them an array of any
                // of the tuple types.
                return (object) [
                    'type' => 'array',
                    'items' => [
                        'oneOf' => array_map(
                            [$this, 'visitTypeForSwagger'],
                            $type->types
                        ),
                    ],
                ];
            case $type instanceof Node\Identifier:
                if (isset($this->classMap[$type->identifier])) {
                    $swaggerType = (object) [
                        'type' => 'object',
                        'properties' => [
                            '_type' => [
                                'type' => 'string',
                                'pattern' => '^' . $type->identifier . '$',
                            ],
                        ],
                    ];

                    $class = $this->classMap[$type->identifier];
                    foreach ($class->properties as $property) {
                        $swaggerType->properties[$property->name] = $this->visitTypeForSwagger($property->type);
                    }

                    return $swaggerType;
                } elseif ($type->identifier === 'array') {
                    return (object) [
                        'type' => $type->identifier,
                        'items' => (object) [],
                    ];
                } elseif ($type->identifier === 'mixed') {
                    return (object) [];
                } else {
                    return (object) [
                        'type' => str_replace(
                            array_keys($this->swaggerTypeMap),
                            array_values($this->swaggerTypeMap),
                            $type->identifier
                        )
                    ];
                }
            case $type instanceof Node\Collection:
                return (object) [
                    'type' => 'array',
                    'items' => $this->visitTypeForSwagger($type->type),
                ];
            case $type instanceof Node\AnyOf:
                return (object) [
                    'oneOf' => array_map(
                        [$this, 'visitTypeForSwagger'],
                        $type->types
                    ),
                ];
            case $type instanceof Node\Generic:
                $swaggerType = (object) [
                    'type' => 'object',
                    'properties' => [
                        '_type' => [
                            'type' => 'string',
                            'pattern' => '^' . $type->identifier->identifier . '$',
                        ],
                    ],
                ];

                if (isset($this->classMap[$type->identifier->identifier])) {
                    $class = $this->classMap[$type->identifier->identifier];
                    $properties = array_replace(
                        $class->properties,
                        $type->properties
                    );
                } else {
                    $properties = $type->properties;
                }

                foreach ($properties as $property) {
                    $swaggerType->properties[(string) ($property->identifier ?? $property->name)] =
                        $this->visitTypeForSwagger($property->type);
                }
                return $swaggerType;
            default:
                throw new \OutOfBoundsException('Unhandled node: ' . get_class($type));
        }
    }
}
