<?php
    include_once __DIR__. "/../config/database.php";
    include_once __DIR__."/../models/modelHome.php";

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(16));
    }

    $category_id = intval($_GET["category"] ?? 0);
    $search = trim($_GET["search"] ?? "");

    $categories = getTopCats($conn);
    $all_categories = getAllCats($conn);
    $sub_categories = array();

    if(isset($_GET["search"]) && !empty($_GET["search"])){
        $contents = searchContents($conn, $search, $category_id);
    }
    else if(isset($_GET["category"]) && $_GET["category"] > 0){
        $contents = getContentsByCategory($conn, $category_id);
    }else{
        $contents = getAllContents($conn);
    }
    if ($category_id > 0) {
        $sub_categories = getSubCats($conn, $category_id);
    }

    include_once __DIR__ . "/../views/viewHome.php";
?>
