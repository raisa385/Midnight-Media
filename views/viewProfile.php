<?php
require_once '/Project/controllers/controlProfile.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <style>
        body{background-color:#0b0f19;color:#f8f9fa;font-family:Arial,sans-serif;padding:40px;}
        .profile-container{background-color:#111827;border:1px solid #1f2937;border-radius:8px;padding:24px;max-width:500px;margin:0 auto;}
        .avatar{width:120px;height:120px;border-radius:50%;object-fit:cover;border:2px solid #2563eb;margin-bottom:16px;}
        .info-group{margin-bottom:12px;font-size:1rem;}
        .label{color:#9ca3af;font-weight:bold;}
        .btn{background-color:#2563eb;color:white;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;text-decoration:none;display:inline-block;margin-top:10px;}
        .btn:hover{background-color:#1d4ed8;}
        .message{font-weight:bold;margin-bottom:15px;color:red;}
        hr{border-color:#1f2937;margin:20px 0;}
    </style>
</head>
<body>
<div class="profile-container">
    <h2>Staff Profile</h2>

    <?php if(isset($_SESSION['flash_msg'])):?>
        <p class="message"><?=htmlspecialchars($_SESSION['flash_msg']);?></p>
        <?php unset($_SESSION['flash_msg']);?>
    <?php endif;?>

    <?php 
        $pic_path="/Project/public/uploads/contents/".$userData['profilePic'];
        if(empty($userData['profilePic'])||!file_exists($_SERVER['DOCUMENT_ROOT'].$pic_path)){
            $pic_src="/Project/assets/defaultprofilepic.png"; 
        }else{
            $pic_src=$pic_path;
        }
    ?>

    <img src="<?=$pic_src;?>" class="avatar" alt="Profile Picture">

    <form action="/Project/controllers/controlProfile.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="profilePic" accept="image/*" required><br> <!--accept only allows image type upload, other tpes are greyed out-->
        <button type="submit" class="btn">Upload photo</button>
    </form>
    <hr>
    <div class="info-group">
        <span class="label">Full Name:</span> <?=htmlspecialchars($userData['name']);?>
    </div>
    <div class="info-group">
        <span class="label">Email Address:</span> <?=htmlspecialchars($userData['email']);?>
    </div>
    <div class="info-group">
        <span class="label">Role:</span><?=htmlspecialchars($userData['userRole']);?>
    </div>
    <a href="/Project/views/viewHome.php" class="btn">Homepage</a>
    <a href="/Project/logout.php" class="btn">Logout</a>
</div>
</body>
</html>