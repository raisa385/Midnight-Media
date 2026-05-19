<?php

include "../config/db.php";

function getAllContents()
{
    global $conn;

    $sql = "SELECT * FROM contents";

    return mysqli_query($conn, $sql);
}

function searchContents($q)
{
    global $conn;

    $sql = "SELECT * FROM contents
            WHERE title LIKE ?
            OR description LIKE ?";

    $stmt = mysqli_prepare($conn, $sql);

    $search = "%$q%";

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $search,
        $search
    );

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

?>