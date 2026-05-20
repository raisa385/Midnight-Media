<?php
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(16));
    }

    include __DIR__."/../config/db.php";
    include __DIR__."/../models/modelHome.php";

    $category_id = intval($_GET["category"] ?? 0);
    $sub_categories = array();

    if ($category_id > 0) {
        $parent_id = getCategoryParentId($conn, $category_id);

        if ($parent_id !== null) {
            $sub_categories = getSubCats($conn, $parent_id);
            $contents = getContentsByCategory($conn, $category_id);
        } else {
            $sub_categories = getSubCats($conn, $category_id);
            if (count($sub_categories) > 0) {
                $contents = getContentsByParent($conn, $category_id);
            } else {
                $contents = getContentsByCategory($conn, $category_id);
            }
        }
    } else {
        $contents = getAllContents($conn);
    }

    $categories = getTopCats($conn);
    $all_categories = getAllCats($conn);

    include __DIR__ . "/../views/viewHome.php";
?>
