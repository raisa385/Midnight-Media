<?php

session_start();

include("../config/db.php");

$id = $_GET['id'];

$status = $_GET['status'];

$sql = "UPDATE content_requests
        SET status=?
        WHERE id=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "si", $status, $id);

mysqli_stmt_execute($stmt);

header("Location: ../views/moderator/requests.php");

?>