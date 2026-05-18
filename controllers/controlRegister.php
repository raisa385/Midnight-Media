<?php 
    session_start();
    include __DIR__."/../config/database.php";
    include __DIR__."/../models/modelRegister.php";

    if($_SERVER['REQUEST_METHOD']==="POST"){
        $name=$_POST['name'];
        $email=$_POST['email'];
        $password=$_POST['password'];
        $userRole=$_POST['userRole'];

        $result=checkEmail($conn, $email);
        if($result===false){
            $_SESSION['flash_msg'] = "Email already in use. Try another.";
            header("Location: viewRegister.php");
            exit();
        }

        if(strlen($password)<8){
            $_SESSION['flash_msg'] = "Password must be at least 8 characters long";
            header("Location: viewRegister.php");
            exit();
        }
        $password = password_hash($password,PASSWORD_DEFAULT);
        
        if($name=='' || $email=='' || $password==''|| $userRole==''){
            $_SESSION['flash_msg'] = "Please fill in all required fields";
            header("Location: viewRegister.php");
            exit();
        }

        $result= registerUser($conn,$name,$email,$password,$userRole);
        if($result===false){
            $_SESSION['flash_msg'] = "Server error: Failed to Register. Please try again.";
            header("Location: viewRegister.php");
            exit();
        }
        $_SESSION['flash_msg'] = "Registration successful. Redirecting...";
        header("Location: viewLogin.php");
    }