<?php

    $bytes = openssl_random_pseudo_bytes(32, $cstrong);
    $hex   = bin2hex($bytes);

    echo "Lengths: Bytes: 32 and Hex: " . strlen($hex) . PHP_EOL;
    var_dump($hex);
    var_dump($cstrong);
    echo PHP_EOL;

?>