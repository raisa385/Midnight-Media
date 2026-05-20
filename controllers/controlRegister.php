<?php 
    session_start();
    include __DIR__."/../config/db.php";
    include __DIR__."/../models/modelRegister.php";

    if($_SERVER['REQUEST_METHOD']==="POST"){

        if(!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != ($_SESSION["csrf_token"] ?? "")) {
            $_SESSION["flash_msg"] = "Invalid form request.";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];
        $role = $_POST["role"];
        if($name=="" || $email=="" || $password=="" || $confirm_password=="" || $role==""){
            $_SESSION["flash_msg"] = "Please fill all fields";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION["flash_msg"] = "Please enter a valid email.";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        if(strlen($password)<8){
            $_SESSION["flash_msg"] = "Password must be at least 8 characters long";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        if($password != $confirm_password){
            $_SESSION["flash_msg"] = "Passwords do not match";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        if($role != "admin" && $role != "moderator"){
            $_SESSION["flash_msg"] = "Please choose a valid role.";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        if(checkEmail($conn, $email) > 0){
            $_SESSION["flash_msg"] = "Email already in use.";
            header("Location: ../views/viewRegister.php");
            exit();
        }

        // Pass the plain text password variable directly here since your model hashes it
        $result = registerUser($conn, $name, $email, $password, $role);
        
        if($result===false){
            $_SESSION['flash_msg'] = "Registration failed";
            header("Location: ../views/viewRegister.php");
            exit();
        }
        else{
            $_SESSION['flash_msg'] = "Registration successful. You can now login.";
        }
        header("Location: ../views/viewLogin.php");
        exit();
    }
?>
