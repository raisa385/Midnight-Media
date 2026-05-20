<?php
    session_start();

    if (!isset($_SESSION["csrf_token"])){
        $_SESSION["csrf_token"] = bin2hex(random_bytes(16));
    }
    if (!isset($_SESSION['user_id'])) {
        $_SESSION["flash_msg"] = "Please login first";
        header("Location: ../views/viewLogin.php");
        exit();
    }
    include __DIR__ . "/../config/database.php";
    include __DIR__ . "/../models/modelProfile.php";

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePic'])){
        $file = $_FILES['profilePic']; //stores all file related info in this (size, type, path, ext, errors)
        
        if($file['error'] !== UPLOAD_ERR_OK){
            $_SESSION['flash_msg'] = "Error: couldn't upload file.";
            header("Location: /Project/views/auth/viewProfile.php");
            exit();
        }

        if ($file['size'] > 2*1024*1024){
        $_SESSION['flash_msg'] = "File size must be under 2MB.";
        header("Location: /Project/views/auth/viewProfile.php");
        exit();
    }

        $allowed_extensions=['jpg', 'jpeg', 'png'];
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']); //pic stored in temp directory in server
        finfo_close($finfo);

        if (!in_array($extension, $allowed_extensions)){
            $_SESSION['flash_msg'] = "Invalid file type. Only: JPG, JPEG or PNG";
            header("Location: /Project/views/auth/viewProfile.php");
            exit();
        }
        $new_filename = "profilePic_" . $_SESSION['user_id']."_".time().".".$extension;
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/Project/public/uploads/contents/';
        
        if (!is_dir($upload_dir)){
            mkdir($upload_dir, 0755, true);
        }
        $destination = $upload_dir.$new_filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            updateProfilePicture($conn, $_SESSION['user_id'], $new_filename);
            $_SESSION['flash_msg'] = "Profile picture updated successfully";
        } else {
            $_SESSION['flash_msg'] = "Error: failed to save file";
        }
        header("Location: /Project/views/auth/viewProfile.php");
        exit();
    }

    $userData = getUserById($conn, $_SESSION['user_id']);
    
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])){
        $action = $_POST['action'];
        if($action === 'update_info'){
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            if($name == "" || $email == ""){
                $_SESSION['flash_msg'] = "Please fill all fields.";
                header("Location: /Project/views/viewProfile.php");
                exit();
            }
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $_SESSION['flash_msg'] = "Please enter a valid email.";
                header("Location: /Project/views/viewProfile.php");
                exit();
            }
            if(updateProfile($conn, $_SESSION['user_id'], $name, $email)){
                $_SESSION['flash_msg'] = "Profile updated successfully";
            } else {
                $_SESSION['flash_msg'] = "Error: failed to update profile";
            }
        } elseif ($action === 'change_password') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['flash_msg'] = "Please fill all password fields.";
                header("Location: /Project/views/viewProfile.php");
                exit();
            }
            if (strlen($new_password) < 8) {
                $_SESSION['flash_msg'] = "New password must be at least 8 characters long.";
                header("Location: /Project/views/viewProfile.php");
                exit();
            }
            if ($new_password !== $confirm_password) {
                $_SESSION['flash_msg'] = "New password and confirmation do not match.";
                header("Location: /Project/views/viewProfile.php");
                exit();
            }
            if (!password_verify($current_password, $userData['password_hash'])) {
                $_SESSION['flash_msg'] = "Current password is incorrect.";
                header("Location: /Project/views/viewProfile.php");
                exit();
            }
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            if (updatePassword($conn, $_SESSION['user_id'], $new_password_hash)) {
                $_SESSION['flash_msg'] = "Password changed successfully";
            } else {
                $_SESSION['flash_msg'] = "Error: failed to change password";
            }
        }
    }
?>