<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


class User{

    private $db;
    private $user;

    public function __construct($username){
        $this->db = Database::getConnection();
        $this->username = $username;
        $sql = "SELECT * FROM `auth` WHERE `username` = '$this->username' OR `email` = '$this->username'";
        $result = mysqli_query($this->db, $sql);
        if(mysqli_num_rows($result) == 1){
            $this->user = $result->fetch_assoc();
        } else {
            throw new Exception("User Not Found");
        }
    }

    public function getUserName(){
        return $this->user['username'];
    }

    public function getPasswordHash(){
        return $this->user['password'];
    }

    public function getEmail(){
        return $this->user['email'];
    }
    public function isActive(){
        return $this->user['active'];
    }

}