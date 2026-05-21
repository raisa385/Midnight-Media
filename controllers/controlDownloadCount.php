<?php
    include_once __DIR__. "/../config/database.php";
    include_once __DIR__."/../models/modelHome.php";
    
    header('Content-Type: application/json');
    $tagID = intval($_POST['content_id'] ?? 0);

    if ($tagID <= 0) {
        echo json_encode(["success" => false]);
        exit();
    }

    $updateCount = updateDownloadCount($conn,$tagID);
    $currentCount = getDownloadCount($conn, $tagID);
    if ($updateCount) {
        echo json_encode(["success" => true, "download_count" => $currentCount]);
    } else {
        echo json_encode(["success" => false]);
    }
?>
