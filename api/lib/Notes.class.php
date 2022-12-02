<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Folder.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Share.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


use Carbon\Carbon;

class Notes extends Share{

    private $db;
    private $data;
    private $id = NULL;

    public function __construct($id = NULL){
        $this->db = Database::getConnection();
        parent::__construct($id, 'note');

        if($id != NULL){
            $this->id = $id;
            $this->refresh();
        }
    }

    public function createNew($title,$body,$folder){
        new Folder($folder);
        if(isset($_SESSION['username']) and strlen($title) <= 45 ){
            $query = "INSERT INTO `notes` (`title`, `body`, `created_at`, `updated_at`, `owner`, `folder_id`) 
            VALUES ('$title', '$body', now(), now(), '$_SESSION[username]', '$folder')";
            if(mysqli_query($this->db, $query)){
               $this->id = mysqli_insert_id($this->db);
               $this->refresh();
               return $this->id;
            }
        } else {
            throw new Exception("Cannot create notes");
        }     
    }
    public function refresh()
    {
        if ($this->id != null) {
            $query = "SELECT * FROM `notes` WHERE `id` = $this->id";
            $result = mysqli_query($this->db, $query);
            if ($result && mysqli_num_rows($result) == 1) {
                $this->data = mysqli_fetch_assoc($result);
                if ($this->getOwner() != $_SESSION['username']) {
                    throw new Exception("Unauthorized");
                }
                $this->id = $this->data['id'];
            } else {
                throw new Exception("Not found");
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

    public function getFolderId(){
        if(isset($this->data) and $this->data['folder_id']){
            return $this->data['folder_id'];
        }
    }

    public function createdAt(){
        if($this->data and isset($this->data['created_at'])){
            $c = new Carbon($this->data['created_at'],date_default_timezone_get());
            return $c->diffForHumans();
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
                $query = "DELETE FROM `notes` WHERE (`id` = '$this->id')";
                $result = mysqli_query($this->db, $query);
                $this->refresh();
                return $result;
            } else {
                throw new Exception("Not Deleted");
            }
        } else {
            throw new Exception("1Unauthorized");
        }

    }



}