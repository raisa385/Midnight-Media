<?php

session_start();

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator'){
    header("Location: ../../login.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Midnight Media - Upload Content</title>
</head>

<body>

<h2>Upload New Content</h2>

<form action="../../controllers/upload_content.php" method="POST" enctype="multipart/form-data">

    <input type="text" name="title" placeholder="Enter Title">

    <br><br>

    <textarea name="description" placeholder="Enter Description"></textarea>

    <br><br>

    <input type="number" name="category_id" placeholder="Enter Category ID">

    <br><br>

    <input type="file" name="media">

    <br><br>

    <button type="submit">Upload</button>

</form>

</body>
</html>