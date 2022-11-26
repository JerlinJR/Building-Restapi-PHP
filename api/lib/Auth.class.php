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
    private $logInTokens;
    private $token;
    private $oauth;

    public function __construct($username, $password = NULL){
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
            $this->oauth = new OAuth($this->token);
            $this->oauth->authenticate();
        } else {
            $user = new User($this->username); //Check wheather the user is avaliable or not
            $hash = $user->getPasswordHash();
            $this->username = $user->getUserName();
            if(password_verify($this->password,$hash)){
                if(!$user->isActive()){
                    throw new Exception("Please Check your email and activate your account");
                }
                $this->logInTokens = $this->addSession(7200);
                //generate Token
            } else {
                throw new Exception("Password Mismatch");
            }
        }

    }

    public function getUsername(){
        if($this->oauth->authenticate()) {
            return $this->oauth->getUsername();
        } 
    }

    private function addSession(){
        $oauth = new OAuth();
        $oauth->setUsername($this->username);
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

    public function getOAuth(){
        return $this->oauth;
    }



}