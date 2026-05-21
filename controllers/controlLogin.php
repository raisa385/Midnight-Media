<?php
    session_start();
    include __DIR__ . "/../config/database.php";
    include __DIR__ . "/../models/modelLogin.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != ($_SESSION["csrf_token"] ?? "")){//prevents crosssierequest forgery
            $_SESSION["flash_msg"] = "Invalid request";
            header("Location: ../views/viewLogin.php");
            exit();
        }

        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        //server-side validation
        if ($email == "" || $password == "") {
            $_SESSION["flash_msg"] = "Please fill all fields";
            header("Location: ../views/viewLogin.php");
            exit();
        }

        $user = getUserData($conn, $email);

        if (!$user || !password_verify($password, $user["password_hash"])) {
            $_SESSION["flash_msg"] = "Invalid email or password.";
            header("Location: ../views/viewLogin.php");
            exit();
        }

        $_SESSION['user_id'] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["userRole"] = $user["userRole"];
    //setting cookie (just email)
        if (isset($_POST["remember"])) {
            setcookie("remember", $user["email"], time() + (30*24*60*60), "/"); //'/' global access
        }

        if ($user["userRole"] == "admin") {
            header("Location: ../index.php?page=admin&action=dashboard");
        }else if($user["userRole"] == "moderator"){
            header("Location: ../views/moderator/dashboard.php");
        }
        exit();
    }
    header("Location: ../views/viewLogin.php");
    exit();
?>
