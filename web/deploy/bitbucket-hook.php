<?php

exec('cd $SICES_PATH && cli/ces-server-update');

$date = date('m/d/Y h:i:s a');
$log = "Date: $date | Commit: MERGED \n";
file_put_contents(__DIR__.'/deploy.log', $log , FILE_APPEND);
