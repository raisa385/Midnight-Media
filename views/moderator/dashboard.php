<?php

session_start();


$_SESSION['userRole'] = 'moderator';

$_SESSION['user_id'] = 1;

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator'){
    header("Location: ../../login.php");
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Midnight Media - Moderator Dashboard</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<h1>Midnight Media - Moderator Panel</h1>

<br>

<a href="upload.php">Upload Content</a>

<a href="contents.php">View Contents</a>

<a href="requests.php">View Requests</a>

<a href="../viewHome.php">Home</a>

</body>
</html>