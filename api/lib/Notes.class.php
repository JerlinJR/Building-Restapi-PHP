<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Share.class.php");



class Notes extends Share{

    public function __construct($id = NULL){
        parent::__construct($id, 'note');
    }

    public static function getAllNotes(){

    }

}