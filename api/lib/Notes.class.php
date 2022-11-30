<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Share.class.php");



class Notes extends Share{

    private $db;
    private $id;


    public function __construct($id = NULL){
        $this->db = Database::getConnection();
        parent::__construct($id, 'note');

        if(id != NULL){
            $this->id = $id;
            $this->refresh();
        }
    }

    public function createNew($title,$body,$folder){
        //
    }

    public function refresh(){
        if($this->id != Null){
            $query = "SELECT * FROM `notes` WHERE `id` = '$this->id';";
            $result = mysqli_query($this->db,$query);
            if($result){
                $this->data = mysqli_fetch_assoc($result);
                $this->id = $this->data['id'];
            } else {
                throw new Exception("Notes not found");
            }
        }
    }

    public function getOwner(){
        if(isset($this->data) and $this->data['owner']){
            return $this->data['owner'];
        }
    }

    public function getBody(){
        if(isset($this->data) and $this->data['body']){
            return $this->data['body'];
        }
    }

    public function setBody($body){
        if(isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']){
            if($this->id){
                $query = "UPDATE `notes` SET `body` = '$body' WHERE (`id` = '$this->id')";
                $result = mysqli_query($this->db, $query);
                $this->refresh();
                return $result;
            } else {
                throw new Exception("Body not set");
            }
        } else {
            throw new Exception("Unauthorized");
        }
    }

    public function getTitle(){
        if(isset($this->data) and $this->data['title']){
            return $this->data['title'];
        }
    }

    public function setTitle($title){
        if(isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']){
            if($this->id){
                $query = "UPDATE `notes` SET `title` = '$title' WHERE (`id` = '$this->id')";
                $result = mysqli_query($this->db, $query);
                $this->refresh();
                return $result;
            } else {
                throw new Exception("Title not set");
            }
        } else {
            throw new Exception("Unauthorized");
        }
    }


    public function delete(){
        if(isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']){
            if($this->id){
                $query = "DELETE FROM `notes` WHERE (`id` = $this->id')";
                $result = mysqli_query($this->db, $query);
                $this->refresh();
                return $result;
            } else {
                throw new Exception("Not Deleted");
            }
        } else {
            throw new Exception("Unauthorized");
        }

    }



    public static function getAllNotes(){

    }

}