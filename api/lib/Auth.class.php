<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/OAuth.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/User.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

class Auth{

    private $username;
    private $user;
    private $db;
    private $isTokenAuth = false;
    private $loginInTokens;

    public function __construct($username,$password = NULL){
        $this->db = Database::getConnection();
        if($password == NULL){
            //Token Based Auth
            $this->token = $username;
            $this->isTokenAuth = true;
        } else {
            //Password Based Auth
            $this->username = $username;
            $this->password = $password;
        }

        if($this->isTokenAuth){
            throw new Exception("Not Implemented");
            
        } else {
            $user = new User($this->username);
            $hash = $user->getPasswordHash();
            $this->username = $user->getUserName();
            if(password_verify($this->password,$hash)){
                if(!$user->isActive()){
                    throw new Exception("Please Check your email and activate your account");
                }
                $this->logInTokens = $this->addSession();
                //generate Token
            } else {
                throw new Exception("Password Mismatch");
            }
        }

    }

    private function addSession(){
        $oauth = new OAuth($this->username);
        $session = $oauth->newSession();
        return $session;
    }


    public static function generateToken($len){
        $bytes = openssl_random_pseudo_bytes($len, $cstrong);
        return bin2hex($bytes);
    
    }

    public function getAuthTokens(){
        return $this->logInTokens;
    }



}