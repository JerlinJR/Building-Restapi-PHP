<?php

require_once("Database.class.php");

class Signup{

    public function __construct($username,$password,$email){

        $this->db = Database::getConnection();
        

    }

}