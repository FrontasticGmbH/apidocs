#  ParamTag

**Fully Qualified**: [`\Frontastic\Apidocs\PhpDoc\ParamTag`](../../../src/php/PhpDoc/ParamTag.php)

**Extends**: [`Tag`](../Tag.md)

Property|Type|Default|Description
--------|----|-------|-----------
`name`|``||
`type`|``||

## Methods

* [__construct()](#__construct)
* [parseTypes()](#parsetypes)

### __construct()

```php
public function __construct(
    string $value
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$value`|`string`||

Return Value: `mixed`

### parseTypes()

```php
public function parseTypes(
    TypeParser $parser,
    ?string $fileName = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$parser`|[`TypeParser`](../TypeParser.md)||
`$fileName`|`?string`|`null`|

Return Value: `mixed`

