<?php

require_once("Database.class.php");

class Signup{

    public function __construct($username,$password,$email){

        $this->db = Database::getConnection();
        $this->username = $username;
        $this->password = $password;
        $this->email = $email; 
    }

    public function getInsertID(){

    }

    public function hashPassword(){
        $options = [
            'cost'=> 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT,$options);
    }


}