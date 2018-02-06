<?php

$content = file_get_contents(dirname(__FILE__) . '/base.TXT');

$extracted = explode("\n", $content);

$data = [];
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

}

print_r($data);
