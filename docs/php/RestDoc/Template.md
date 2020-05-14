#  Template

**Fully Qualified**: [`\Frontastic\Apidocs\RestDoc\Template`](../../../src/php/RestDoc/Template.php)

## Methods

* [__construct()](#__construct)
* [renderType()](#rendertype)
* [render()](#render)

### __construct()

```php
public function __construct(
    FileTools $fileTools,
    array $classMap
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$fileTools`|[`FileTools`](../FileTools.md)||
`$classMap`|`array`||

Return Value: `mixed`

### renderType()

```php
public function renderType(
    Node $type,
    mixed $indentation,
    mixed $skipIndent = false
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$type`|[`Node`](../TypeParser/Node.md)||
`$indentation`|`mixed`||
`$skipIndent`|`mixed`|`false`|

Return Value: `void`

### render()

```php
public function render(
    string $targetFile,
    object $entity,
    array $paths,
    string $relativeSourceLocation
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$targetFile`|`string`||
`$entity`|`object`||
`$paths`|`array`||
`$relativeSourceLocation`|`string`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
