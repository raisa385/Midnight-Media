<?php

session_start();

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator'){
    header("Location: ../viewLogin.php");
    exit();
}

include("../../config/db.php");



$sql = "SELECT * FROM contents";



if(isset($_GET['search'])){

    $search = $_GET['search'];

    $sql = "SELECT * FROM contents
            WHERE title LIKE '%$search%'";
}



$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Midnight Media - All Contents</title>
</head>

<body>

<h2>All Uploaded Contents</h2>



<form method="GET">

    <input type="text" name="search" placeholder="Search Content">

    <button type="submit">Search</button>

</form>

<br>



<table border="1" cellpadding="10">

<tr>

    <th>Title</th>

    <th>Description</th>

    <th>Action</th>

</tr>



<?php

while($row = mysqli_fetch_assoc($result)){

?>

<tr>

    <td>

        <?php echo htmlspecialchars($row['title']); ?>

    </td>

    <td>

        <?php echo htmlspecialchars($row['description']); ?>

    </td>

    <td>

        <a href="../../controllers/delete_content.php?id=<?php echo $row['id']; ?>">

            Delete

        </a>

    </td>

</tr>

<?php

}

?>

</table>

</body>
</html>
