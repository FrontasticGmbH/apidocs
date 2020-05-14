#  Template

**Fully Qualified**: [`\Frontastic\Apidocs\PhpDoc\Template`](../../../src/php/PhpDoc/Template.php)

## Methods

* [__construct()](#__construct)
* [linkOwn()](#linkown)
* [addClassToIndex()](#addclasstoindex)
* [render()](#render)

### __construct()

```php
public function __construct(
    FileTools $fileTools
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$fileTools`|[`FileTools`](../FileTools.md)||

Return Value: `mixed`

### linkOwn()

```php
public function linkOwn(
    string $from,
    string $input
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$from`|`string`||
`$input`|`string`||

Return Value: `string`

### addClassToIndex()

```php
public function addClassToIndex(
    string $className,
    string $file
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$className`|`string`||
`$file`|`string`||

Return Value: `void`

### render()

```php
public function render(
    string $targetFile,
    object $entity,
    string $relativeSourceLocation
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$targetFile`|`string`||
`$entity`|`object`||
`$relativeSourceLocation`|`string`||

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
