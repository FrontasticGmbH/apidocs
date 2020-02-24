#  Template

Fully Qualified: [`\Frontastic\Apidocs\Template`](../../src/php/Template.php)

## Methods

* [__construct()](#__construct)
* [e()](#e)
* [w()](#w)
* [makeAnchor()](#makeanchor)
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
`$fileTools`|[`FileTools`](FileTools.md)||

Return Value: `mixed`

### e()

```php
public function e(
    string $text
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$text`|`string`||

Return Value: `mixed`

### w()

```php
public function w(
    string $text
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$text`|`string`||

Return Value: `mixed`

### makeAnchor()

```php
public function makeAnchor(
    string $heading
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$heading`|`string`||

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
    array $methods,
    array $properties,
    string $relativeSourceLocation
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$targetFile`|`string`||
`$entity`|`object`||
`$methods`|`array`||
`$properties`|`array`||
`$relativeSourceLocation`|`string`||

Return Value: `mixed`

