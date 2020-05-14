#  Parser

**Fully Qualified**: [`\Frontastic\Apidocs\TypeParser\Parser`](../../../src/php/TypeParser/Parser.php)

## Methods

* [parse()](#parse)

### parse()

```php
public function parse(
    array $tokens
): Node
```

*Parse token stream.*

Parse an array of ezcDocumentBBCodeToken objects into a bbcode abstract
syntax tree.

Argument|Type|Default|Description
--------|----|-------|-----------
`$tokens`|`array`||

Return Value: [`Node`](Node.md)

