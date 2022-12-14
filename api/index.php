<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once($_SERVER['DOCUMENT_ROOT']."/api/REST.api.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Signup.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/User.class.php");

    class API extends REST {

        public $data = "";

        private $db = NULL;
        private $current_call;
        private $auth;

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
            else {
                if(isset($_GET['namespace'])){
                    $dir = $_SERVER['DOCUMENT_ROOT'] .'/api/apis/'.$_GET['namespace'];
                    $file = $dir.'/'.$func.'.php';
                    if(file_exists($file)){
                            include $file;
                            $this->current_call = Closure::bind(${$func}, $this, get_class());
                            $this->func();
                    } 
                
                    /**
                     * Use this snippet
                     * When you want to include multiple file @ same time 
                     * <-----START----->
                     */

                    // $methods = scandir($dir);
                    // var_dump($methods);
                    // foreach($methods as $m){
                    //     if($m == "." or $m == ".."){
                    //         continue;
                    //     }
                    //     $basem = basename($m, '.php');
                    //     // echo "Trying to call $basem() for $func()\n";
                    //     if($basem == $func){
                    //         include $dir."/".$m;
                    //         $this->current_call = Closure::bind(${$basem}, $this, get_class());
                    //         $this->basem();
                    //     }
                    // }
                    /*.
                     * <-----END----->
                     */

                } else {
                    $this->response($this->json(['error' => 'method_not_found']),404);
                }
            }
        }

        public function auth(){

            $header = getallheaders();
            if(isset($header['Authorization'])){
                $token = explode(' ',$header['Authorization']);
                $this->auth = new Auth($token[1]);
            }
        }

        public function isAuthenticated(){
            if($this->auth == null){
                return false;
            }
            
            if($this->auth->getOAuth()->authenticate() and isset($_SESSION['username'])){
                return true;
            } else {
            return false;
            }
        }

        public function getUsername(){
            return $_SESSION['username'];
        }

        public function die($e){
            $data = [
                "error" => $e->getMessage()
            ];
            $data = $this->json($data);
            $this->response($data,400);
        }

        public function __call($method,$args){

            if(is_callable($this->current_call)){
                return call_user_func_array($this->current_call, $args);
            } else {
                $this->response($this->json(['error' => 'method_not_callable']),404);
            }
        }


        /*************API SPACE START*******************/

        private function about(){

            if($this->get_request_method() != "POST"){
                $error = array('status' => 'WRONG_CALL','method' => $this->get_request_method(), "msg" => "The type of call cannot be accepted by our servers.");
                $error = $this->json($error);
                $this->response($error,406);
            }
            $data = array('version' => $this->_request['version'],'method' => $this->get_request_method(), 'desc' => 'This API is created by Blovia Technologies Pvt. Ltd., for the public usage for accessing data about vehicles.');
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
                $s = new Signup("",$this->_request['pass'],"");
                $hash = $s->hashPassword($cost);
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
    try{
        $api->auth();
        $api->processApi();
    } catch (Exception $e) {
        $api->die($e);
    }

?>