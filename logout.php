<?php
    session_start();
    session_unset();
    session_destroy();
    setcookie("remember","",time()-1,"/");
    header("Location: controllers/controlHome.php");
    exit();
?>
