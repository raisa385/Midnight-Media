<?php

header("Location: views/home.php");

?>
session_start();
//$_SESSION['user_id'] = 1;
//$_SESSION['name']    = 'Admin';
//$_SESSION['role']    = 'admin';

require_once 'config/db.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/ModeratorController.php';
require_once 'controllers/MemberController.php';

$page   = $_GET['page']   ?? 'home';
$action = $_GET['action'] ?? 'index';

// Route requests to the correct controller
switch ($page) {
    case 'home':
        $ctrl = new MemberController();
        $ctrl->index();
        break;

    case 'auth':
        $ctrl = new AuthController();
        if ($action === 'login')    $ctrl->login();
        elseif ($action === 'register') $ctrl->register();
        elseif ($action === 'logout')   $ctrl->logout();
        else $ctrl->login();
        break;

    case 'admin':
        $ctrl = new AdminController();
        if ($action === 'dashboard')         $ctrl->dashboard();
        elseif ($action === 'moderators')    $ctrl->listModerators();
        elseif ($action === 'add_moderator') $ctrl->addModerator();
        elseif ($action === 'delete_moderator') $ctrl->deleteModerator();
        elseif ($action === 'contents')      $ctrl->listContents();
        elseif ($action === 'upload')        $ctrl->uploadContent();
        elseif ($action === 'edit')          $ctrl->editContent();
        elseif ($action === 'delete_content') $ctrl->deleteContent();
        elseif ($action === 'requests')      $ctrl->viewRequests();
        else $ctrl->dashboard();
        break;

    case 'moderator':
        $ctrl = new ModeratorController();
        if ($action === 'dashboard')  $ctrl->dashboard();
        elseif ($action === 'upload') $ctrl->uploadContent();
        elseif ($action === 'delete') $ctrl->deleteContent();
        elseif ($action === 'requests') $ctrl->viewRequests();
        else $ctrl->dashboard();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page not found</h1>";
}
