#  Tokenizer

**Fully Qualified**: [`\Frontastic\Apidocs\TypeParser\Tokenizer`](../../../src/php/TypeParser/Tokenizer.php)

## Methods

* [__construct()](#__construct)
* [tokenizeFile()](#tokenizefile)
* [tokenizeString()](#tokenizestring)

### __construct()

```php
public function __construct(): mixed
```

Return Value: `mixed`

### tokenizeFile()

```php
public function tokenizeFile(
    string $file
): array
```

*Tokenize the given file*

The method tries to tokenize the passed file and returns an array of
tokens.

Argument|Type|Default|Description
--------|----|-------|-----------
`$file`|`string`||

Return Value: `array`

### tokenizeString()

```php
public function tokenizeString(
    string $string
): array
```

*Tokenize the given string*

The method tries to tokenize the passed string and returns an array of
tokens.

Argument|Type|Default|Description
--------|----|-------|-----------
`$string`|`string`||

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
