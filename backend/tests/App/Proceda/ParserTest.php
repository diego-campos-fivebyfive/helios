<?php

use Tests\App\Sices\SicesTest;

/**
 * @group parser_ocoren
 */
class ParserTest extends SicesTest
{
    public function testParser()
    {
        // TODO: O arquivo de teste utilizado encontra-se em docs/uml/proceda/sample/base.TXT
        $filename = dirname(__FILE__) . '/base.TXT';

        $array = \App\Proceda\Parser::fromFile($filename);

        self::assertEquals(542, $array[0]['code']);

        $content = file_get_contents($filename);

        $array2 = \App\Proceda\Parser::fromContent($content);

        self::assertEquals(542, $array2[0]['code']);

        $array3 = \App\Proceda\Parser::fromArray(explode("\n", $content));

        self::assertEquals(542, $array3[0]['code']);
    }
}
