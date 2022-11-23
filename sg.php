<pre>
<?php

// print_r($GLOBALS);
// print_r($_SERVER);
// print_r($_REQUEST);
// print_r($_POST);
// print_r($_GET);
// print_r($_FILES);
// print_r($_ENV);
// print_r($_COOKIE);

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/User.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


try{

    $user = new User("Jerlin");
    echo $user->getEmail();
    echo $user->getPasswordHash();


} catch(Exception $e) {
    echo $e->getMessage();
}

?>
</pre>