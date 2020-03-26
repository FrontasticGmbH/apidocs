<?php

namespace Frontastic\Apidocs;

class TypeParserTest extends \PHPUnit\Framework\TestCase
{
    public function getTypesToParse()
    {
        return array(
            ['string', 'string'],
            ['void', 'void'],
            ['MyClass', 'MyClass'],
            ['\MyClass', '\MyClass'],
            ['\My\Class', '\My\Class'],
            ['My\Class', 'My\Class'],
            ['string|int', 'string|int'],
            ['string[]', 'string[]'],
            ['?string', '?string'],
            ['?string[]', '?string[]'],
            ['?string|int', '?string|int'],
            ['[string]', '[string]'],
            ['[string, int]', '[string, int]'],
            ['[?string, int, ?int]', '[?string, int, ?int]'],
            ['[string, int, ?int|string]', '[string, int, ?int|string]'],
            ['[string, My\Class[], ?int|string]', '[string, My\Class[], ?int|string]'],
            ['Result{}', 'Result'],
            ['Result{items: Product[]}', 'Result{ items: Product[] }'],
            ['Result{items: Product[], query: Query}', 'Result{ items: Product[], query: Query }'],
            ['?Result{items: Product[], query: ?Query, tuple: [string, int, ?int|string]}', '?Result{ items: Product[], query: ?Query, tuple: [string, int, ?int|string] }'],
        );
    }

    /**
     * @dataProvider getTypesToParse
     */
    public function testParseTypes(string $input, $output)
    {
        $typeParser = new TypeParser();
        $type = $typeParser->parse($input);

        $this->assertTrue($type instanceof TypeParser\Node\Type);
        $this->assertEquals($output, (string) $type);
    }
}
