<?php

namespace App\Proceda;

/**
 * Class Parser
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Parser
{
    const ACCEPTED_EVENTS = ['000', '001', '002', '031', '105'];

    /**
     * @param $filename
     * @return array
     */
    public static function fromFile($filename)
    {
        return self::fromContent(file_get_contents($filename));
    }

    /**
     * @param $content
     * @return array
     */
    public static function fromContent($content)
    {
        return self::fromArray(explode("\n", $content));
    }

    /**
     * @param $array
     * @return array
     */
    public static function fromArray($array)
    {
        return
            array_filter(
                array_map(function($line) {
                    return [
                        'code' => substr($line, 0, 3),          // IDENTIFICADOR DE REGISTRO
                        'document' => substr($line, 3, 14),     // CNPJ (CGC) DO EMISSOR DA NOTA FISCAL
                        'serial' => substr($line, 14, 3),       // SÉRIE DA NOTA FISCAL
                        'invoice' => substr($line, 20, 9),      // NÚMERO DA NOTA FISCAL
                        'event' => substr($line, 29, 3),        // CÓDIGO DE OCORRÊNCIA NA ENTREGA
                        'date' => substr($line, 32, 8),         // DATA DA OCORRÊNCIA
                        'time' => substr($line, 40, 4)          // HORA DA OCORRÊNCIA
                    ];
                }, $array),
                function ($item) {
                    return $item['code'] == 542 && in_array($item['event'], self::ACCEPTED_EVENTS);
                }
            );
    }
}
