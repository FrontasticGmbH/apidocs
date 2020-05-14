#  RestDoc

**Fully Qualified**: [`\Frontastic\Apidocs\RestDoc`](../../src/php/RestDoc.php)

## Methods

* [__construct()](#__construct)
* [render()](#render)
* [getIndex()](#getindex)
* [getConfiguration()](#getconfiguration)

### __construct()

```php
public function __construct(
    string $configurationFile,
    TypeParser $typeParser,
    PhpDoc $phpDoc,
    array $formatter = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$configurationFile`|`string`||
`$typeParser`|[`TypeParser`](TypeParser.md)||
`$phpDoc`|[`PhpDoc`](PhpDoc.md)||
`$formatter`|`array`|`[]`|

Return Value: `mixed`

### render()

```php
public function render(): void
```

Return Value: `void`

### getIndex()

```php
public function getIndex(): string
```

Return Value: `string`

### getConfiguration()

```php
public function getConfiguration(): object
```

Return Value: `object`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
