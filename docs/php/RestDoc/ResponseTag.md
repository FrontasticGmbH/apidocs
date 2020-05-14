#  ResponseTag

**Fully Qualified**: [`\Frontastic\Apidocs\RestDoc\ResponseTag`](../../../src/php/RestDoc/ResponseTag.php)

**Extends**: [`Tag`](../Tag.md)

Property|Type|Default|Description
--------|----|-------|-----------
`status`|``||
`bodyType`|``||
`description`|``||

## Methods

* [__construct()](#__construct)
* [parseTypes()](#parsetypes)

### __construct()

```php
public function __construct(
    string $status,
    string $bodyType,
    ?string $description = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$status`|`string`||
`$bodyType`|`string`||
`$description`|`?string`|`null`|

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

