#  RequestTag

**Fully Qualified**: [`\Frontastic\Apidocs\RestDoc\RequestTag`](../../../src/php/RestDoc/RequestTag.php)

**Extends**: [`Tag`](../Tag.md)

Property|Type|Default|Description
--------|----|-------|-----------
`method`|``||
`url`|``||
`bodyType`|``||

## Methods

* [__construct()](#__construct)
* [parseTypes()](#parsetypes)

### __construct()

```php
public function __construct(
    string $method,
    string $url,
    string $bodyType
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$method`|`string`||
`$url`|`string`||
`$bodyType`|`string`||

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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
