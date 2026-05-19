<?php

include "../models/ContentModel.php";

header("Content-Type: application/json");

$q = $_GET['q'];

$result = searchContents($q);

$data = [];

while($row = mysqli_fetch_assoc($result))
{
    $data[] = $row;
}

echo json_encode($data);

?>