<?php

include "../models/RequestModel.php";

if(isset($_POST['submit']))
{
    $title = htmlspecialchars($_POST['title']);

    $category = htmlspecialchars($_POST['category']);

    $message = htmlspecialchars($_POST['message']);

    if(empty($title))
    {
        echo "Title Required";
    }
    else
    {
        addRequest(
            $title,
            $category,
            $message
        );

        echo "Request Added";
    }
}

?>