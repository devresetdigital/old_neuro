<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mc = new Memcached();
$mc->addServer("localhost", 11211);

$mc->set("foo", "Hello!");
$mc->set("bar", "Memcached...");

$arr = array(
    $mc->get("foo"),
    $mc->get("bar")
);
var_dump($arr);
