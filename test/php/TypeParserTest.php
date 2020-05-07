<?php

namespace Frontastic\Apidocs;

class TypeParserTest extends \PHPUnit\Framework\TestCase
{
    public function getTypesToParse()
    {
        return array(
            ['string', 'string'],
            ['void', 'void'],
            ['MyClass', '\MyClass'],
            ['\MyClass', '\MyClass'],
            ['\My\Class', '\My\Class'],
            ['My\Class', '\My\Class'],
            ['string|int', 'string|int'],
            ['string|int|bool', 'string|int|bool'],
            ['string[]', 'string[]'],
            ['?string', '?string'],
            ['?string[]', '?string[]'],
            ['?string|int', '?string|int'],
            ['[string]', '[string]'],
            ['?[string]', '?[string]'],
            ['[string, int]', '[string, int]'],
            ['[?string, int, ?int]', '[?string, int, ?int]'],
            ['[string, int, ?int|string]', '[string, int, ?int|string]'],
            ['[string, My\Class[], ?int|string]', '[string, \My\Class[], ?int|string]'],
            ['array<int, string>', 'array<int, string>'],
            ['?array<string, My\Class[]>', '?array<string, \My\Class[]>'],
            ['array<int, Result{items: Product[]}>|null', 'array<int, \Result{ items: \Product[] }>|null'],
            ['Result{}', '\Result'],
            ['Result{items: Product[]}', '\Result{ items: \Product[] }'],
            ['Result{items: Product[], query: Query}', '\Result{ items: \Product[], query: \Query }'],
            ['?Result{items: Product[], query: ?Query, tuple: [string, int, ?int|string]}', '?\Result{ items: \Product[], query: ?\Query, tuple: [string, int, ?int|string] }'],
        );
    }

    /**
     * @dataProvider getTypesToParse
     */
    public function testParseTypes(string $input, string $output)
    {
        $typeParser = new TypeParser();
        $type = $typeParser->parse($input);

        $this->assertTrue($type instanceof TypeParser\Node\Type);
        $this->assertEquals($output, (string) $type);
    }

    public function getTypeParseErrors()
    {
        return array(
            ['int string', 'Error parsing type int string in file –: Expected single node left on document stack at EOF.'],
            ['array<int, int, int>', 'Error parsing type array<int, int, int> in file –: A map expects exactly two items (key & value).'],
            ['{items: string[]}', 'Error parsing type {items: string[]} in file –: Generic object properties can only be defined on an identifier.'],
            ['?{items: string[]}', 'Error parsing type ?{items: string[]} in file –: Generic object properties can only be defined on an identifier.'],
        );
    }

    /**
     * @dataProvider getTypeParseErrors
     */
    public function testParseErrors(string $input, string $error)
    {
        $typeParser = new TypeParser();
        try {
            $type = $typeParser->parse($input);
        } catch (\RuntimeException $e) {
            $this->assertSame($error, $e->getMessage());
            return;
        }

        $this->fail('Expected exception with message: ' . $error);
    }
}
