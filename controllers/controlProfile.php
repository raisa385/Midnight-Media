<?php
    session_start();
    if (!isset($_SESSION['userID'])) {
        $_SESSION['flash_msg'] = "Please log in to view your profile.";
        header("Location: ../views/auth/viewLogin.php");
        exit();
    }
    include _DIR_.'/../config/database.php';
    require_once _DIR_.'/../models/modelProfile.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePic'])){
        $file = $_FILES['profilePic'];
        
        if($file['error'] !== UPLOAD_ERR_OK){
            $_SESSION['flash_msg'] = "Error: couldn't upload file.";
            header("Location: ../views/auth/viewProfile.php");
            exit();
        }

        if ($file['size'] > 2*1024*1024){
        $_SESSION['flash_msg'] = "File size must be under 2MB.";
        header("Location: ../views/auth/viewProfile.php");
        exit();
    }

        $allowed_extensions=['jpg', 'jpeg', 'png'];
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tempName']);
        finfo_close($finfo);

        if (!in_array($extension, $allowed_extensions)){
            $_SESSION['flash_msg'] = "Invalid file type. Only: JPG, JPEG or PNG";
            header("Location: ../views/auth/viewProfile.php");
            exit();
        }

        $new_filename = "profilePic_" . $SESSION['userID']."_".time().".".$extension;
        $upload_dir = _DIR_ . '/../../public/uploads/contents/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $destination = $upload_dir.$new_filename;

        if (move_uploaded_file($file['tempName'], $destination)) {
            updateProfilePicture($conn, $_SESSION['userID'], $new_filename);
            $_SESSION['flash_msg'] = "Profile picture updated successfully";
        } else {
            $_SESSION['flash_msg'] = "Error: failed to save file";
        }
        header("Location: ../views/auth/viewProfile.php");
        exit();
    }
    $userData = getUserById($conn, $_SESSION['user_id']);
?>