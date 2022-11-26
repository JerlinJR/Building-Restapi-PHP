<?php

// echo date_default_timezone_get();

// date_default_timezone_set('Asia/Kolkata');

// echo date_default_timezone_get();

// echo date("Y-m-d h:i:s a", time());

// $createdAt = strtotime();
// $valid_for = $data['valid_for'];
// $time = $createdAt+$valid_for;
// echo $time;


session_start();
print_r($_SESSION);
$_SESSION['username'] = "Jerlin";