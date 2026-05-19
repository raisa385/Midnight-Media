<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "ftp_server"
);

if(!$conn)
{
    die("Database Connection Failed");
}

?>