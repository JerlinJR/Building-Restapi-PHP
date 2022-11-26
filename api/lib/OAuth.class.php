<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/User.class.php");


class OAuth{
    
    private $db;
    private $refresh_token = NULL;
    private $acess_token = NULL;
    private $valid_for;
    private $username;
    private $user;

    public function __construct($token = NULL){
        $this->db = Database::getConnection();
        if($token != NULL){
            if($this->startsWith($token,'a.')){
                $this->acess_token = $token;
            } else if($this->startsWith($token,'r.')) {
                $this->refresh_token = $token;
            } else {
                $this->setUsername($token);
            }
        }


    }

    public function setUsername($username){
        $this->username = $username;
        $this->user = new User($username);
    }

    public function getUsername(){
        return $this->username;
    }

    public function authenticate(){
        if ($this->acess_token != null) {
            $query = "SELECT * FROM `session` WHERE `access_token` = '$this->acess_token';";
            $result = mysqli_query($this->db, $query);
            if ($result) {
                $data = mysqli_fetch_assoc($result);
                $created_at = strtotime($data['created_at']);
                $expires_at = $created_at + $data['valid_for'];
                if (time() <= $expires_at) {
                    if(session_status() === PHP_SESSION_NONE){
                        session_start();
                    }
                    $this->username = $_SESSION['username'] = $data['username'];
                    $_SESSION['token'] = $this->acess_token;
                    return true;
                } else {
                    throw new Exception("Expired token");
                }
            } else {
                throw new Exception(mysqli_error($this->db));
            }
        }

    }

    public function newSession($valid_for = 7200,$refrence_token = 'auth_grant'){
        if($this->username == NULL){
            throw new Exception("Username not set for OAuth");
        }
        $this->valid_for = $valid_for;
        $acess_token = 'a.'.Auth::generateToken(32);
        if($refrence_token == 'auth_grant'){
            $this->refresh_token = 'r.'.Auth::generateToken(32);
        } else {
            $this->refresh_token = 'd.'.Auth::generateToken(16);
        }

        $sql = "INSERT INTO `session` (`username`, `access_token`, `created_at`, `valid`, `refresh_token`, `valid_for`, `reference_token`)
                VALUES ('$this->username', '$acess_token', now(), '1', '$this->refresh_token' , '$this->valid_for', 'auth_grant');";
        if(mysqli_query($this->db,$sql)){
            return array(
                "acess_token" => $acess_token,
                "valid for" => $valid_for,
                "refresh_token" => $this->refresh_token,
                "refrence_token" => $refrence_token,
                "type" => 'Api'
            );
        } else {
            throw new Exception("Unable to create Session");
        }
    }

    public function refreshAcess(){
        if($this->refresh_token != NULL and !$this->startsWith($this->refresh_token,'d.')){
            $sql = "SELECT * FROM `session` WHERE `refresh_token` = '$this->refresh_token';";
            $result = mysqli_query($this->db,$sql);
            if($result){
                $data = mysqli_fetch_assoc($result);
                $this->username = $data['username'];
                // var_dump($data);
                if($data['valid'] == 1){
                    return $this->newSession(7200, $this->refresh_token);
                } else {  
                    throw new Exception("Expired Token");
                }
            } else {
                throw new Exception("Error:"+mysqli_error($this->db));
            }
        } else {
            throw new Exception("Invalid Request");
        }
    }

    private function startsWith ($string, $startString){
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }





}