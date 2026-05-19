<?php
    session_start();
    session_unset();
    session_destroy();
    if(isset($_COOKIE['remember'])){
        setcookie("remember","",time()-1,"/");
    }
    session_start();
    $_SESSION['flash_msg']="You have been logged out successfully.";
    header("Location: views/auth/viewLogin.php");
    exit();
?>