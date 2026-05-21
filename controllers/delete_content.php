<?php

session_start();

// Auth check
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator') {
    header("Location: ../views/moderator/dashboard.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../views/moderator/contents.php");
    exit;
}

// CSRF check
if (empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    die("Invalid request. Please go back and try again.");
}

include("../config/db.php");

$id = filter_input(INPUT_POST, 'content_id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash'] = "Invalid content ID.";
    header("Location: ../views/moderator/contents.php");
    exit;
}


$stmt = mysqli_prepare($conn, "SELECT file_path FROM contents WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res  = mysqli_stmt_get_result($stmt);
$row  = mysqli_fetch_assoc($res);


$del = mysqli_prepare($conn, "DELETE FROM contents WHERE id=?");
mysqli_stmt_bind_param($del, 'i', $id);
mysqli_stmt_execute($del);


if ($row && !empty($row['file_path'])) {
    $fullPath = __DIR__ . '/../' . $row['file_path'];
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

$_SESSION['flash'] = "Content deleted successfully.";
header("Location: ../views/moderator/contents.php");
exit;
