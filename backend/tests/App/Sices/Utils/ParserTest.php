<?php

namespace Tests\App\Sices\Utils;

use App\Sices\Utils\Parser;
use Tests\App\Sices\SicesTest;

/**
 * @group sices_utils_parser
 */
class ParserTest extends SicesTest
{
    /**
     * Test parser strategies
     */
    public function testFromMapping()
    {
        $mapping = [
            'foo' => 2,
            'bar' => 5,
            'baz' => 8
        ];

        // Test with intantiator
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $data = Parser::from($mapping)->parse($str);

        $this->assertEquals('AB', $data['foo']);
        $this->assertEquals('CDEFG', $data['bar']);
        $this->assertEquals('HIJKLMNO', $data['baz']);

        // Test with definition
        $numbers = '12345678910111213';
        $data2 = Parser::from($mapping, $numbers);

        $this->assertEquals('12', $data2['foo']);
        $this->assertEquals('34567', $data2['bar']);
        $this->assertEquals('89101112', $data2['baz']);
    }
}
