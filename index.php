<?php
session_start();

$page = $_GET['page'] ?? '';
$action = $_GET['action'] ?? '';

if ($page === 'admin') {
    require_once __DIR__ . '/controllers/AdminController.php';
    $admin = new AdminController();

    if ($action === 'moderators') {
        $admin->listModerators();
    } elseif ($action === 'add_moderator') {
        $admin->addModerator();
    } elseif ($action === 'contents') {
        $admin->listContents();
    } elseif ($action === 'upload') {
        $admin->uploadContent();
    } elseif ($action === 'edit') {
        $admin->editContent();
    } elseif ($action === 'delete_content') {
        $admin->deleteContent();
    } elseif ($action === 'delete_moderator') {
        $admin->deleteModerator();
    } elseif ($action === 'requests') {
        $admin->viewRequests();
    } else {
        $admin->dashboard();
    }
    exit();
}

if ($page === 'auth' && $action === 'login') {
    header("Location: views/viewLogin.php");
    exit();
}

if ($page === 'auth' && $action === 'logout') {
    header("Location: logout.php");
    exit();
}

header("Location: controllers/controlHome.php");
exit();
?>
