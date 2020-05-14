#  FileTools

**Fully Qualified**: [`\Frontastic\Apidocs\FileTools`](../../src/php/FileTools.php)

## Methods

* [__construct()](#__construct)
* [makeAbsolute()](#makeabsolute)
* [getFiles()](#getfiles)
* [getRelativePath()](#getrelativepath)

### __construct()

```php
public function __construct(
    string $rootPath
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$rootPath`|`string`||

Return Value: `mixed`

### makeAbsolute()

```php
public function makeAbsolute(
    string $path
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$path`|`string`||

Return Value: `string`

### getFiles()

```php
public function getFiles(
    string $pattern
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$pattern`|`string`||

Return Value: `array`

### getRelativePath()

```php
public function getRelativePath(
    string $source,
    string $target
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$source`|`string`||
`$target`|`string`||

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
