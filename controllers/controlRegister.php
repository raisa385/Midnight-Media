<?php 
    session_start();
    include __DIR__."/../config/database.php";
    include __DIR__."/../models/modelRegister.php";

    if($_SERVER['REQUEST_METHOD']==="POST"){
        $name=$_POST['name'];
        $email=$_POST['email'];
        $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
        $userRole=$_POST['userRole'];

        $result=checkEmail($conn, $email);
        if($result!=0){//row returned so email already exists
            $_SESSION['flash_msg'] = "Email already in use. Try another.";
            header("Location: ../views/viewRegister.php");
            exit();
        }

        $result= registerUser($conn,$name,$email,$password,$userRole);
        if($result===false){
            $_SESSION['flash_msg'] = "Server error: Failed to Register. Please try again.";
            header("Location: ../views/viewRegister.php");
            exit();
        }

        $_SESSION['flash_msg'] = "Registration successful. Redirecting...";
        header("Location: ../views/viewLogin.php");
    }