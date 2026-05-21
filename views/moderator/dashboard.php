<?php

session_start();

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator'){
    header("Location: ../viewLogin.php");
    exit();
}

include("../../config/db.php");

// Dashboard stats
$totalContents  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM contents"))['c'];
$myUploads      = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM contents WHERE uploader_id=" . (int)$_SESSION['user_id']))['c'];
$pendingReqs    = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM content_requests WHERE request_status='pending'"))['c'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Midnight Media - Moderator Dashboard</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .stats { display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap; }
        .stat-box { background: #1c1c1c; border: 1px solid #00bfff; border-radius: 8px;
                    padding: 20px 30px; text-align: center; min-width: 140px; }
        .stat-box .num { font-size: 2rem; color: #00bfff; font-weight: bold; }
        .stat-box .lbl { color: #aaa; font-size: 0.85rem; margin-top: 4px; }
        .nav-links { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    </style>
</head>

<body>

<h1>Midnight Media - Moderator Panel</h1>

<div class="nav-links">
    <a href="../../views/viewHome.php">Home</a>
    <a href="upload.php">Upload Content</a>
    <a href="contents.php">View Contents</a>
    <a href="requests.php">View Requests</a>
    <a href="../../logout.php">Logout</a>
</div>

<h2>Dashboard</h2>

<div class="stats">
    <div class="stat-box">
        <div class="num"><?= (int)$totalContents ?></div>
        <div class="lbl">Total Contents</div>
    </div>
    <div class="stat-box">
        <div class="num"><?= (int)$myUploads ?></div>
        <div class="lbl">My Uploads</div>
    </div>
    <div class="stat-box">
        <div class="num"><?= (int)$pendingReqs ?></div>
        <div class="lbl">Pending Requests</div>
    </div>
</div>

<div style="display:flex; gap:10px; flex-wrap:wrap;">
    <a href="upload.php">⬆️ Upload New Content</a>
    <a href="contents.php">🗂 Manage Contents</a>
    <a href="requests.php">📬 View Requests</a>
</div>

</body>
</html>
