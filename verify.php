<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/Signup.class.php';
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");


$token = mysqli_real_escape_string(Database::getConnection(), $_GET['token']);

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Sql Injection</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/navbar-fixed/">

    

    <!-- Bootstrap core CSS -->
<link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Show it is fixed to the top */
    body {
        min-height: 75rem;
        padding-top: 4.5rem;
    }

      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="navbar-top-fixed.css" rel="stylesheet">
  </head>
  <body>

  <?php
  
  try{

    if(Signup::verifyAccount($token)){
        echo "Verified";
    } else {
        echo "Cannot Verify";
    }

    } catch(Exception $e){
    // echo "Already Verified";
    ?>

    <main class="container">
        <div class="bg-light p-5 rounded">
        <h1>Logged In Sucessfully</h1>
        <p class="lead">Make changes here</p>
        <a class="btn btn-lg btn-primary" href="../components/navbar/" role="button">Logout &raquo;</a>
        </div>
    </main>

    <?php
    }
    ?>


    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>

      
  </body>
</html>
