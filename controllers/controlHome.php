<?php
    if(session_status()===PHP_SESSION_NONE){
        session_start();
    }
    include '/Project/config/database.php';
    require_once '/Project/models/modelHome.php';
    $categories=getTopCats($conn);
    include '/Project/views/viewHome.php';
?>