# `abstract`  Formatter

**Fully Qualified**: [`\Frontastic\Apidocs\RestDoc\Formatter`](../../../src/php/RestDoc/Formatter.php)

## Methods

* [getContentType()](#getcontenttype)
* [setClassMap()](#setclassmap)
* [getSchema()](#getschema)

### getContentType()

```php
abstract public function getContentType(): string
```

Return Value: `string`

### setClassMap()

```php
abstract public function setClassMap(
    array $classMap
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$classMap`|`array`||

Return Value: `void`

### getSchema()

```php
abstract public function getSchema(
    Node $type
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$type`|[`Node`](../TypeParser/Node.md)||

Return Value: `array`

