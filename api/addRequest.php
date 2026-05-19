<?php

include "../models/RequestModel.php";

header("Content-Type: application/json");

$title = htmlspecialchars($_POST['title']);

$category = htmlspecialchars($_POST['category']);

$message = htmlspecialchars($_POST['message']);

if(empty($title))
{
    echo json_encode([
        "message" => "Title Required"
    ]);

    exit();
}

addRequest(
    $title,
    $category,
    $message
);

echo json_encode([
    "message" => "Request Submitted Successfully"
]);

?>