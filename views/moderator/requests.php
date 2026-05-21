<?php

session_start();

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator'){
    header("Location: ../viewLogin.php");
    exit();
}

include("../../config/db.php");

$sql = "SELECT * FROM content_requests";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
   <title>Midnight Media - Content Requests</title>
</head>

<body>

<h2>Content Requests</h2>

<table border="1" cellpadding="10">

<tr>

    <th>Content Title</th>

    <th>Category</th>

    <th>Message</th>

    <th>Status</th>

    <th>Action</th>

</tr>



<?php

while($row = mysqli_fetch_assoc($result)){

?>

<tr>

    <td>

        <?php echo htmlspecialchars($row['content_title']); ?>

    </td>

    <td>

        <?php echo htmlspecialchars($row['category_requested']); ?>

    </td>

    <td>

        <?php echo htmlspecialchars($row['message']); ?>

    </td>

    <td>

        <?php echo htmlspecialchars($row['status']); ?>

    </td>

    <td>

        <a href="../../controllers/update_request.php?id=<?php echo $row['id']; ?>&status=fulfilled">

            Fulfilled

        </a>

        |

        <a href="../../controllers/update_request.php?id=<?php echo $row['id']; ?>&status=rejected">

            Rejected

        </a>

    </td>

</tr>

<?php

}

?>

</table>

</body>
</html>
