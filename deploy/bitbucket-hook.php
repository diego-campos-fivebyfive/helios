<?php

function getPaths ()
{
  return (object)[
    "git" => "/var/www",
    "root" => "/var/www",
  ];
}

function createLog ($commit, $branch)
{
  $date = date('m/d/Y h:i:s a');
  $log = "Date: $date | Commit: $commit | Branch: $branch \n";
  file_put_contents('deploy.log', $log , FILE_APPEND);
}

function updateHomolog ($branch = 'No Branch')
{
  $path = getPaths();

  exec("cd $path->git && git fetch");
  exec("cd $path->git && GIT_WORK_TREE=$path->root git checkout -f");

  $commit = shell_exec("cd $path->git && git rev-parse --short HEAD");
  createLog($commit, $branch);
}

function updateCases ($commits)
{
  if (empty($commits)) {
    updateHomolog();
    return;
  }

  foreach ($commits as $commit) {

    $branch = $commit->branch;
    $branches = $commit->branches;

    if ($branch || isset($branches)) {
      updateHomolog($branch);
      break;
    }
  }
}

$payload = $_POST['payload'];
if (isset($payload)) {
  $commits = json_decode($payload)->commits;
  updateCases($commits);
}
