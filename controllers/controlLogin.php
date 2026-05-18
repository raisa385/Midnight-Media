<?php
    session_start();
    include __DIR__."/../config/database.php";
    include __DIR__."/../models/modelLogin.php";
    if($_SERVER['REQUEST_METHOD']==="POST"){
        $email=$_POST['email'];
        $password = password_hash($_POST['password'],PASSWORD_DEFAULT);

        if(emty($email) || empty($password)){
            $_SESSION['flash_msg'] = "Please fill in all fields";
            header("Location: ../views/viewLogin.php");
            exit();
        }

        $user= getUserData($conn, $email);
        if(!$user){
            $_SESSION['flash_msg'] = "Invalid email";
            header("Location: ../views/viewLogin.php");
            exit();
        }
        if(password_verify($password,$user['password_hash'])){
            $_SESSION['userID']=$user['id'];
            $_SESSION['username']=$user['name'];
            $_SESSION['role']=$user['userRole'];

            if(isset($_POST['remember'])=="checked"){
                $userData = ['id'=>$_SESSION['userID'],'username'=>$_SESSION['name'],'role'=>$_SESSION['userRole']]
                $cookies = json_encode($userData);
                setcookie("remember",$cookies,time()+30*24*60*60,"/");
            }
            header("Location:../views/viewHomepage.php");
            exit();
        }
        else{
            $_SESSION['flash_msg']="Invalid password";
            header("Location:../views/viewLogin.php");
            exit();
        }

    }