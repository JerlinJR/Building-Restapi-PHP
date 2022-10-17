<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once($_SERVER['DOCUMENT_ROOT']."/api/REST.api.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Signup.class.php");
    



    class API extends REST {

        public $data = "";

        private $db = NULL;

        public function __construct(){
            parent::__construct();                // Init parent contructor
            $this->db = Database::getConnection();// Initiate Database connection
        }

        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else 
                $this->response('',400);                // If the method not exist with in this class, response would be "Page not found".
        }

        /*************API SPACE START*******************/

        private function about(){

            if($this->get_request_method() != "POST"){
                $error = array('status' => 'WRONG_CALL', "msg" => "The type of call cannot be accepted by our servers.");
                $error = $this->json($error);
                $this->response($error,406);
            }
            $data = array('version' => $this->_request['version'], 'desc' => 'This API is created by Blovia Technologies Pvt. Ltd., for the public usage for accessing data about vehicles.');
            $data = $this->json($data);
            $this->response($data,200);

        }

        private function verify(){
            if($this->get_request_method() == "POST" and isset($this->_request['user']) and isset($this->_request['pass'])){
                $user = $this->_request['user'];
                $password =  $this->_request['pass'];

                $flag = 0;
                if($user == "admin"){
                    if($password == "adminpass123"){
                        $flag = 1;
                    }
                }

                if($flag == 1){
                    $data = [
                        "status" => "verified"
                    ];
                    $data = $this->json($data);
                    $this->response($data,200);
                } else {
                    $data = [
                        "status" => "unauthorized"
                    ];
                    $data = $this->json($data);
                    $this->response($data,401);
                }
            } else {
                $data = [
                        "status" => "bad_request"
                    ];
                    $data = $this->json($data);
                    $this->response($data,400);
            }
        }

        private function test(){
                $data = $this->json(getallheaders());
                $this->response($data,200);
        }

        private function request_info(){
            $data = $this->json($_SERVER);
        }

        private function gen_hash(){
            $st = microtime(true);
            if(isset($this->_request['pass'])){
                $cost = (int)$this->_request['cost'];
                $data = password_hash($this->_request['pass'], PASSWORD_BCRYPT);
                $data = [
                    "hash" => $hash,
                    "val" => $this->_request['pass'],
                    "info" => password_get_info($hash),
                    "verify_status" => password_verify($this->_request['pass'],$hash),
                    "time" => microtime(true) - $st
                ];
                $data = $this->json($data);
                $this->response($data,200);
            } else {
                echo "Pass the value";
            }
        }

        private function verify_hash(){
            $st = microtime(true);
            if(isset($this->_request['pass']) and isset($this->_request['hash'])){
            $data = [
                "password" => $this->_request['pass'],
                "hash" => $this->_request['hash'],
                "info" => password_get_info($hash),
                "verify_status" => password_verify($this->_request['pass'],$this->_request['hash']),
                "time" => microtime(true) - $st
            ];
            $data = $this->json($data);
            $this->response($data,200);
            }
        }

        private function signup(){

            if($this->get_request_method() == "POST" and isset($this->_request['username']) and isset($this->_request['email']) and isset($this->_request['password'])){
                $username = $this->_request['username'];
                $email = $this->_request['email'];
                $password = $this->_request['password'];

                try{
                    $s = new Signup($username,$password,$email);
                    $data = [
                        "message" => "Signup Sucess",
                        "userid" => $s->getInsertID()
                    ];
                    $this->response($this->json($data),200);
                } catch(Exception $e) {
                    $data = [
                        "error" => $e->getMessage()
                    ];
                    $this->response($this->json($data),409);

                }

            } else {
                $data = [
                    "errorr" => "Bad request"
                ];
                $data = $this->json($data);
                $this->response($data,400);
            }

        }






        /*************API SPACE END*********************/

        /*
            Encode array into JSON
        */
        private function json($data){
            if(is_array($data)){
                return json_encode($data, JSON_PRETTY_PRINT);
            } else {
                return "{}";
            }
        }

    }

    // Initiiate Library

    $api = new API;
    $api->processApi();
?>