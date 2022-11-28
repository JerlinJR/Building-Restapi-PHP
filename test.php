<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Folder.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


session_start();
$_SESSION['username'] = "Jerlin1";

$f = new Folder(12);

echo $f->getName();
$f->rename("Hello");
echo $f->getName();
