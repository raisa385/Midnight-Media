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
        $_SESSION["userID"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["username"] = $user["name"];
        $_SESSION["userRole"] = $user["role"];

        if (isset($_POST["remember"])) {
            setcookie("remember", $user["email"], time() + (30*24*60*60), "/"); // '/' allows global acess
        }

        if ($user["role"] == "admin") {
            header("Location: ../views/admin/dashboard.php");
        }else{
            header("Location: ../views/moderator/dashboard.php");
        }
        exit();
    }
    header("Location: ../views/viewLogin.php");
    exit();
?>
