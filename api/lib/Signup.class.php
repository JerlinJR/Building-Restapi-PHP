<?php

require_once("Database.class.php");
require '../vendor/autoload.php';

class Signup{

    public function __construct($username,$password,$email){

        $this->db = Database::getConnection();
        $this->username = $username;
        $this->password = $password;
        $this->email = $email; 

        // if($this->userExists($username,$password,$email)){

        // }

        $bytes = random_bytes(16);
        $this->token = $token = bin2hex($bytes);
        $password = $this->hashPassword();

        $sql = "INSERT INTO `auth` (`username`, `password`, `email`, `active`, `token`, `signup_time`)
        VALUES ('$username', '$password', '$email', '0','$token',now());";
        // echo $sql;
        if(!mysqli_query($this->db,$sql)){
            Throw new Exception("Sorry,Unable to Signup");
        } else {
            $this->id = mysqli_insert_id($this->db);
            $this->sendEmailVerification();
        }
    }

    public function sendEmailVerification(){
        $config_json = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../env.json');
        $config = json_decode($config_json, true);

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@jerlin.space", "API Test");
        $email->setSubject("Verify your account");
        $email->addTo($this->email, $this->username);
        $email->addContent("text/plain", "Please verify your account at : https://".$config['domain']."/verify?token=$token");
        $email->addContent(
            "text/html", "<strong>Please verify your account at : <a href=\"https://".$config['domain']."/verify?token=$token\">clicking here</a></strong>"
        );
        $sendgrid = new \SendGrid($config['email_api']);
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";
        } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }

    public function getInsertID(){
        return $this->id;
    }

    public function userExists(){
        return false;
    }

    public function hashPassword($cost = 10){
        $options = [
            'cost'=> $cost,
        ];
        return password_hash($this->password, PASSWORD_BCRYPT,$options);
    }


}