<?php

namespace Frontastic\Apidocs;

class TypeParserTest extends \PHPUnit\Framework\TestCase
{
    public function getTypesToParse()
    {
        return array(
            ['string', 'string'],
            ['void', 'string'],
            ['string|int', 'string'],
        );
    }

    /**
     * @dataProvider getTypesToParse
     */
    public function testParseTypes(string $input, $output)
    {
        $typeParser = new TypeParser();

        $this->assertEquals(
            $output,
            $typeParser->parse($input)
        );
    }
}
