<?php

session_start();

include("../config/db.php");

$id = $_GET['id'];



// Get File Path

$get = "SELECT * FROM contents WHERE id=?";

$stmt = mysqli_prepare($conn, $get);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);



// Delete File

unlink($row['file_path']);



// Delete Database Record

$sql = "DELETE FROM contents WHERE id=?";

$stmt2 = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt2, "i", $id);

mysqli_stmt_execute($stmt2);



header("Location: ../views/moderator/contents.php");

?>