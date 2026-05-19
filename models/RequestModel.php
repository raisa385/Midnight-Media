<?php

include "../config/db.php";

function addRequest($title, $category, $message)
{
    global $conn;

    $sql = "INSERT INTO content_requests
            (
                content_title,
                category_requested,
                message,
                status
            )
            VALUES
            (?, ?, ?, 'pending')";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $title,
        $category,
        $message
    );

    return mysqli_stmt_execute($stmt);
}

?>