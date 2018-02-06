<?php

$content = file_get_contents(dirname(__FILE__) . '/base.TXT');

$data = array_reduce(explode("\n", $content), function ($carry, $line) {
    return $carry[] = [
        'code' => substr($line, 0, 3),          // IDENTIFICADOR DE REGISTRO
        'document' => substr($line, 3, 14),     // CNPJ (CGC) DO EMISSOR DA NOTA FISCAL
        'serial' => substr($line, 14, 3),       // SÉRIE DA NOTA FISCAL
        'invoice' => substr($line, 20, 9),      // NÚMERO DA NOTA FISCAL
        'event' => substr($line, 29, 3),        // CÓDIGO DE OCORRÊNCIA NA ENTREGA
        'date' => substr($line, 32, 8),         // DATA DA OCORRÊNCIA
        'time' => substr($line, 40, 4)          // HORA DA OCORRÊNCIA
    ];
}, []);


/**
 *  Processo similar (menos eficiente)
 *
 foreach ($extracted as $line => $info) {
  $code = substr($info, 0, 3);
  $document = substr($info, 3, 14);
  $serial = substr($info, 14, 3);
  $invoice = substr($info, 20, 9);
  $event = substr($info, 29, 3);
  $date = substr($info, 32, 8);
  $time = substr($info, 40, 4);

  $data[] = [
    'code' => $code,            // IDENTIFICADOR DE REGISTRO
    'document' => $document,    // CNPJ (CGC) DO EMISSOR DA NOTA FISCAL
    'serial' => $serial,        // SÉRIE DA NOTA FISCAL
    'invoice' => $invoice,      // NÚMERO DA NOTA FISCAL
    'event' => $event,          // CÓDIGO DE OCORRÊNCIA NA ENTREGA
    'date' => $date,            // DATA DA OCORRÊNCIA
    'time' => $time             // HORA DA OCORRÊNCIA
  ];
}*/

print_r($data);
