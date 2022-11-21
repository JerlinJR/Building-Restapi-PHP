<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/Signup.class.php';

$token = $_GET['token'];
try{

    if(Signup::verifyAccount($token)){
        echo "Verified";
    } else {
        echo "Cannot Verify";
    }

} catch(Exception $e){
    echo "Already Verified";
}