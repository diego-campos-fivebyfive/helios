<?php

$path = (object)[
  "git" => "git@bitbucket.org:cjchamado/sices.git",
  "root" => "/var/www",
];

exec("cd $path->git && git fetch");
exec("cd $path->git && GIT_WORK_TREE=$path->root git checkout -f");
$commit = shell_exec("cd $path->git && git rev-parse --short HEAD");

$date = date('m/d/Y h:i:s a');
$log = "Date: $date | Commit: $commit \n";
file_put_contents('deploy.log', $log , FILE_APPEND);
