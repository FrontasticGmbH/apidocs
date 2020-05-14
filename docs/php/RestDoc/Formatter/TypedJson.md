#  TypedJson

**Fully Qualified**: [`\Frontastic\Apidocs\RestDoc\Formatter\TypedJson`](../../../../src/php/RestDoc/Formatter/TypedJson.php)

**Extends**: [`Formatter`](../Formatter.md)

## Methods

* [getContentType()](#getcontenttype)
* [setClassMap()](#setclassmap)
* [getSchema()](#getschema)

### getContentType()

```php
public function getContentType(): string
```

Return Value: `string`

### setClassMap()

```php
public function setClassMap(
    array $classMap
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$classMap`|`array`||

Return Value: `void`

### getSchema()

```php
public function getSchema(
    Node $type,
    bool $required = false
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$type`|[`Node`](../../TypeParser/Node.md)||
`$required`|`bool`|`false`|

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
