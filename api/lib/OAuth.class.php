<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/User.class.php");


class OAuth{
    
    private $db;
    private $refresh_token;
    private $valid_for;
    private $username;

    public function __construct($username,$refresh_token = NULL){
        $this->refresh_token = $refresh_token;
        $this->db = Database::getConnection();
        $this->username = $username;
        $u = new User($username);

    }

    public function newSession($valid_for = 7200){
        $this->valid_for = $valid_for;
        $acess_token = Auth::generateToken(32);
        $this->refresh_token = Auth::generateToken(32);

        $sql = "INSERT INTO `session` (`username`, `access_token`, `created_at`, `valid`, `refresh_token`, `valid_for`, `reference_token`)
                VALUES ('$this->username', '$acess_token', now(), '1', '$this->refresh_token' , '$this->valid_for', 'auth_grant');";
        if(mysqli_query($this->db,$sql)){
            return array(
                "acess_token" => $acess_token,
                "valid for" => $valid_for,
                "refresh_token" => $this->refresh_token,
                "type" => 'Api'
            );
        } else {
            throw new Exception("Unable to create Session");
        }
    }

    public function refreshAcess(){
        if($this->refresh_token){
            $sql = "SELECT * FROM `session` WHERE `refresh_token` = '$this->refresh_token';";
            $result = mysqli_query($this->db,$sql);
            if($result){
                $data = mysqli_fetch_assoc($result);
                if($data['valid' == 1]){

                } else {
                    throw new Exception("Expired Token");
                }
            } else {
                throw new Exception("Invalid Request");
            }
        } 
    }



}