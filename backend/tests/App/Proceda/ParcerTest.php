<?php

use Tests\App\Sices\SicesTest;

/**
 * @group parcer_ocoren
 */
class ParcerTest extends SicesTest
{
    public function testParcer()
    {
        // TODO: O arquivo de teste utilizado encontra-se em docs/uml/proceda/sample/base.TXT
        $filename = dirname(__FILE__) . '/base.TXT';

        $array = \App\Proceda\Parcer::fromFile($filename);

        self::assertEquals(542, $array[0]['code']);

        $content = file_get_contents($filename);

        $array2 = \App\Proceda\Parcer::fromContent($content);

        self::assertEquals(542, $array2[0]['code']);

        $data = array_map(function($line) {
            return [
                'code' => substr($line, 0, 3),          // IDENTIFICADOR DE REGISTRO
                'document' => substr($line, 3, 14),     // CNPJ (CGC) DO EMISSOR DA NOTA FISCAL
                'serial' => substr($line, 14, 3),       // SÉRIE DA NOTA FISCAL
                'invoice' => substr($line, 20, 9),      // NÚMERO DA NOTA FISCAL
                'event' => substr($line, 29, 3),        // CÓDIGO DE OCORRÊNCIA NA ENTREGA
                'date' => substr($line, 32, 8),         // DATA DA OCORRÊNCIA
                'time' => substr($line, 40, 4)          // HORA DA OCORRÊNCIA
            ];

        }, explode("\n", $content));

        $array3 = \App\Proceda\Parcer::fromArray($data);

        self::assertEquals(542, $array3[0]['code']);
    }
}
