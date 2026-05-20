<?php
    session_start();
    session_unset();
    session_destroy();
    setcookie("remember","",time()-1,"/");
    session_start();
    $_SESSION["flash_msg"] = "You have been logged out.";
    header("Location: views/viewLogin.php");
    exit();
?>
