<?php
$role = $_SESSION["userRole"] ?? "member";
?>
<nav class="navbar">
    <div class="logo-container">
        <img src="/Project/assets/MM_logo.png" alt="Logo" class="logo-image">
        <span class="logo-text">Midnight Media</span>
    </div>
    <div class="n-items">
        <a href="../controllers/controlHome.php">Home</a>
        <?php if ($role == "admin") { ?><a href="../index.php?page=admin&action=dashboard">Admin Panel</a>
            <a href="../views/viewProfile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        <?php } else if ($role == "moderator") { ?>
                <a href="../views/moderator/dashboard.php">Moderator Panel</a>
                <a href="../views/viewProfile.php">Profile</a>
                <a href="../logout.php">Logout</a>
        <?php } else { ?><a href="../views/viewLogin.php">Admin/Moderator Login</a><?php } ?>
    </div>
</nav>
