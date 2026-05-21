<?php

session_start();

// Auth check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'moderator') {
    header("Location: ../views/moderator/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../views/moderator/upload.php");
    exit;
}

// CSRF check
if (empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    die("Invalid request. Please go back and try again.");
}

include("../config/db.php");

// ── Input sanitization ──
$title       = htmlspecialchars(strip_tags(trim($_POST['title']      ?? '')), ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars(strip_tags(trim($_POST['description'] ?? '')), ENT_QUOTES, 'UTF-8');
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
$uploader_id = (int)$_SESSION['user_id'];

// ── Server-side validation ──
$errors = [];
if (empty($title))       $errors[] = "Title is required.";
if (empty($description)) $errors[] = "Description is required.";
if (!$category_id)       $errors[] = "Please select a category.";

// ── File validation ──
$allowed_ext  = ['mp4', 'mkv', 'avi', 'zip', 'rar', 'exe', 'iso', 'pdf'];
$allowed_mime = [
    'video/mp4', 'video/x-matroska', 'video/x-msvideo',
    'application/zip', 'application/x-rar-compressed', 'application/vnd.rar',
    'application/x-msdownload', 'application/x-cd-image', 'application/pdf'
];
$max_size = 5 * 1024 * 1024 * 1024; // 5 GB

if (empty($_FILES['media']['name'])) {
    $errors[] = "Please select a file to upload.";
} else {
    $file    = $_FILES['media'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime    = mime_content_type($file['tmp_name']);

    if ($file['error'] !== UPLOAD_ERR_OK)      $errors[] = "File upload error.";
    if (!in_array($ext,  $allowed_ext))         $errors[] = "File type not allowed: .$ext";
    if (!in_array($mime, $allowed_mime))        $errors[] = "File MIME type not allowed.";
    if ($file['size'] > $max_size)              $errors[] = "File exceeds 5 GB limit.";
}

if (!empty($errors)) {
    
    $_SESSION['upload_errors'] = $errors;
    header("Location: ../views/moderator/upload.php");
    exit;
}


$safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
$filename  = $safeTitle . '_' . uniqid() . '.' . $ext;
$uploadDir = __DIR__ . '/../public/uploads/contents/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$destination = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    $_SESSION['upload_errors'] = ["Failed to save file. Check folder permissions."];
    header("Location: ../views/moderator/upload.php");
    exit;
}

$filePath = 'public/uploads/contents/' . $filename;


$stmt = mysqli_prepare($conn,
    "INSERT INTO contents (title, description, file_path, category_id, uploader_id)
     VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sssii', $title, $description, $filePath, $category-id, $uploader_id);
mysqli_stmt_execute($stmt);

$_SESSION['flash'] = "'{$title}' uploaded successfully.";
header("Location: ../views/moderator/contents.php");
exit;
