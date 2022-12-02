
<pre>

<?php

require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Folder.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Notes.class.php");
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';


session_start();
$_SESSION['username'] = "Jerlin1";

// $f = new Folder(12);

// echo $f->getName();
// $f->rename("Hello");
// echo $f->getName();


// echo $a->getTitle()."\n";
// echo $a->getOwner()."\n";
// // echo $a->getBody();
// echo $a->getBody()."\n";
// echo $a->createdAt()."\n";


try{
    // $a = new Folder(14);
    // $a->createNew("Test Title","Hello Hello Hello",14);
    // echo $a->countNotes();
    // echo $a->getAllNotes();
    // $a->delete();

    // $a->delete();
    // $a->delete();


    print_r(Folder::getAllFolders());


} catch(Exception $e){
    echo $e->getMessage();
}

?>
</pre>



