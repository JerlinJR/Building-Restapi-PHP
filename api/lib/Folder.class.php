<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Notes.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Share.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


class Folder extends Share{

    private $db;
    private $data;
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
            throw new Exception("Cannot create folder");
        }

    }

    public function getName(){
        if($this->data && $this->data['name']){
            return $this->data['name'];
        }
    }

    public function createdAt(){
        if($this->data and isset($this->data['created_at'])){
            $c = new Carbon($this->data['created_at'],date_default_timezone_get());
            return $c->diffForHumans();
        }
    }

    public function getId(){
        if($this->id){
            return $this->id;
        }
    }

    public function getAllNotes(){
        $query = "SELECT * FROM `notes` WHERE `folder_id` =$this->id;";
        $result = mysqli_query($this->db,$query);
        if($result){
            $data = mysqli_fetch_all($result,MYSQLI_ASSOC);
            return $data;
        } else {
            return [];
        }
        
    }

    public function countNotes(){
        $query = "SELECT COUNT(*) FROM `notes` WHERE `folder_id` = $this->id;";
        $result = mysqli_query($this->db,$query);
        if($result){
            $data = mysqli_fetch_assoc($result);
            return $data['COUNT(*)'];
        }

    }

    public function refresh()
    {
        if ($this->id != null) {
            $query = "SELECT * FROM folders WHERE id=$this->id";
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
        if(isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']){
            $notes = $this->getAllNotes();
            foreach($notes as $note){
                $n = new Notes($note['id']);
                $n->delete();
            }
                if($this->id){
                    $query = "DELETE FROM `folders` WHERE (`id` = '$this->id')";
                    $result = mysqli_query($this->db, $query);
                    $this->refresh();
                    return $result;
                } else {
                    throw new Exception("Folder Not Deleted");
                }
        } else {
            throw new Exception("Unauthorized");
        }

    }


    public static function getAllFolders(){
        $db = Database::getConnection();
        $query = "SELECT * FROM `folders` WHERE `owner` = '$_SESSION[username]'";
        $result = mysqli_query($db,$query);
        if($result){
            $data = mysqli_fetch_all($result,MYSQLI_ASSOC);
            return $data;
        } else {
            return [];
        }
    }

    public function getOwner(){
        if(isset($this->data) and $this->data['owner']){
            return $this->data['owner'];
        }
    }

}