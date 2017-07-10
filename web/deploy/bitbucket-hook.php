<?php

exec('cd $SICES_PATH && ces-update-remote');

$date = date('m/d/Y h:i:s a');
$log = "Date: $date | Commit: 'MERGED' \n";
file_put_contents('deploy.log', $log , FILE_APPEND);
