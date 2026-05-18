<?php

session_start();

include("../config/db.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $title = $_POST['title'];

    $description = $_POST['description'];

    $category_id = $_POST['category_id'];

    $uploader_id = $_SESSION['user_id'];



    // Validation

    if(empty($title) || empty($description) || empty($category_id)){

        die("All Fields Are Required");
    }



    // File Upload

    $fileName = $_FILES['media']['name'];

    $tmpName = $_FILES['media']['tmp_name'];



    // File Extension Check

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ['mp4', 'pdf', 'exe'];

    if(!in_array($fileExt, $allowed)){

        die("Invalid File Type");
    }



    // File Path

    $path = "../public/uploads/contents/" . $fileName;



    // Move File

    move_uploaded_file($tmpName, $path);



    // Insert Query

    $sql = "INSERT INTO contents(title, description, file_path, category_id, uploader_id)

            VALUES(?, ?, ?, ?, ?)";



    $stmt = mysqli_prepare($conn, $sql);



    mysqli_stmt_bind_param($stmt, "sssii",

        $title,

        $description,

        $path,

        $category_id,

        $uploader_id

    );



    mysqli_stmt_execute($stmt);



    header("Location: ../views/moderator/contents.php");

}

?>