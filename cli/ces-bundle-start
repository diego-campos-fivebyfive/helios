<?php

define("ADMIN_MODE", false);
define("SECRET", "abc");

function getNodePid ()
{
  session_start();
  return $_SESSION["nodepid"];
}

function setNodePid ($pid)
{
  session_start();
  $_SESSION["nodepid"]=$pid;
}

function unsetNodePid ()
{
  session_start();
  unset($_SESSION["nodepid"]);
}

function serverStart ()
{
  $existentPid = getNodePid();

  if ($existentPid) {
    echo "There is a current Server Running";
    return;
  }

  $commandStartServer = "SERVER_PORT=2002 node/bin/node index.js";
  $commandPidCallback = "> /dev/null 2>&1 & echo $!;";

  $pid = exec("$commandStartServer $commandPidCallback");

  if (!$pid) {
    echo "Server Start Error";
    return;
  }

  echo "Server Running...";
  setNodePid($pid);
}

function serverStop ()
{
  $pid = getNodePid();

  if (!$pid) {
    echo "There is not a current Server Running";
    return;
  }

  echo "Pid: $pid, Server stopping...";
  passthru("kill $pid", $status);

  if ($status !== 0) {
    echo "Error while stopping Server";
    return;
  }

  echo "Server Stopped";
  unsetNodePid();
}

if (ADMIN_MODE) {
  if (isset($_GET["start"]) && $_GET["start"] === SECRET) serverStart();
  if (isset($_GET["stop"]) && $_GET["stop"] === SECRET) serverStop();
}
else {
  $existentPid = getNodePid();
  echo "Server Mode on, " . (($existentPid) ? "Running Server..." : "Server not Started yet");
}

