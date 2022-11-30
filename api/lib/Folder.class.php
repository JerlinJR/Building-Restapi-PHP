<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Share.class.php");



class Folder extends Share{

    private $db;
    private $data = NULL;
    private $id = NULL;

    public function __construct($id = NULL){
        $this->db = Database::getConnection();
        parent::__construct($id, 'folder');

        if($id != Null){
            $this->id = $id;
            $this->refresh();
        }
    }

    public function createNew($name = "Untitled"){
        if(isset($_SESSION['username']) and strlen($name) <= 45){
            $query = "INSERT INTO `folders` (`name`, `owner`, `created_at`)
            VALUES ('$name', '$_SESSION[username]', now());";
            if(mysqli_query($this->db, $query)){
                $this->id = mysqli_insert_id($this->db);
                return $this->id;
            }
        } else {
            throw new Exception("Cannot create notes");
        }

    }

    public function getName(){
        if($this->data && $this->data['name']){
            return $this->data['name'];
        }
    }

    public function createdAt(){
        if($this->data and isset($this->data['created_at'])){
            return $this->data['created_at'];
        }
    }

    public function getId(){
        if($this->id){
            return $this->id;
        }
    }

    public function countNotes(){
        
    }

    public function getAllNotes(){

    }

    public function refresh(){
        if($this->id != Null){
            $query = "SELECT * FROM `folders` WHERE `id` = '$this->id';";
            $result = mysqli_query($this->db,$query);
            if($result){
                $this->data = mysqli_fetch_assoc($result);
                $this->id = $this->data['id'];
            } else {
                throw new Exception("Folder not found");
            }
        }
    }

    public function  rename($name){
        if($this->id){
            $query = "UPDATE `folders` SET `name` = '$name' WHERE (`id` = '$this->id')";
            $result = mysqli_query($this->db, $query);
            $this->refresh();
            return $result;
        } else {
            throw new Exception("Folder not loaded");
        }
    }

    public function delete(){

    }


    public static function getAllFolders(){

    }

    public function getOwner(){
        if(isset($this->data) and $this->data['owner']){
            return $this->data['owner'];
        }
    }

}