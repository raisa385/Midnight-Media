<?php
    $userRole=$_SESSION['role']??'member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Midnight Media homepage</title>
    <style>
        *{box-sizing:border-box;}
        body{background-color:#0b0f19;color:#f8f9fa;font-family:Arial,sans-serif;display:flex;min-height:100vh;margin:0;padding:0;}
        .sidebar{width:240px;background-color:#111827;border-right:1px solid #1f2937;padding:24px;position:fixed;top:0;bottom:0;left:0;overflow-y:auto;}
        .logo-container{display:flex;align-items:center;margin-bottom:24px;}
        .logo-image{width:32px;height:32px;vertical-align:middle;margin-right:8px;}
        .section-title{font-size:0.75rem;text-transform:uppercase;color:#9ca3af;margin-bottom:12px;font-weight:bold;}
        .nav-list{list-style:none;margin-bottom:20px;padding:0;}
        .nav-link{display:block;color:#9ca3af;text-decoration:none;padding:8px 12px;border-radius:6px;margin-bottom:4px;font-size:0.9rem;}
        .nav-link:hover{color:#ffffff;background-color:#2563eb;}
        .main-content{margin-left:240px;flex:1;padding:24px;display:flex;flex-direction:column;}
        .topbar{background-color:#111827;border:1px solid #1f2937;border-radius:8px;padding:16px;display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
        .status-container{display:flex;align-items:center;gap:16px;}
        .network-status{color:#10b981;font-size:0.8rem;}
        .btn-action{background-color:#2563eb;color:#f8f9fa;text-decoration:none;padding:6px 16px;border-radius:4px;font-size:0.85rem;font-weight:bold;margin-left:8px;}
        .hero-banner{background:linear-gradient(135deg,#1e3a8a 0%,#0f172a 100%);border:1px solid #1e40af;border-radius:8px;padding:32px;margin-bottom:24px;}
        .hero-banner h1{font-size:1.8rem;margin-bottom:8px;}
        .hero-banner p{color:#9ca3af;font-size:0.95rem;}
        .content-heading{font-size:1.2rem;border-left:4px solid #2563eb;padding-left:8px;margin-bottom:16px;}
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="logo-container">
            <img src="/Project/assets/MM_logo.png" alt="Logo" class="logo-image">
            <span class="logo-text">Midnight Media</span>
        </div>
        <hr style="border:0;border-top:1px solid #1f2937;margin-bottom:20px;">
        <p class="section-title">Categories</p>
        <ul class="nav-list">
            <li><a href="/Project/controllers/controlHome.php" class="nav-link hover">All Content</a></li>
            <?php foreach($categories as $cat):?>
                <li><a href="/Project/controllers/controlHome.php?category=<?=$cat['id'];?>" class="nav-link">&bull; <?=htmlspecialchars($cat['name']);?></a></li>
            <?php endforeach;?>
        </ul>
    </nav>
    <div style="display:flex;flex-direction:column;width:100%;">
        <main class="main-content">
            <div class="topbar">
                <div>
                    <span style="font-size:0.95rem;font-weight:bold;">Welcome, <?=htmlspecialchars($_SESSION['username']??'Member');?></span>
                </div>
                <div class="status-container">
                    <?php if($userRole==='member'):?>
                        <a href="/Project/views/viewLogin.php" class="btn-action">Login</a>
                    <?php endif;?>
                    <?php if($userRole==='admin'):?>
                        <a href="/Project/views/viewProfile.php" class="btn-action">My profile</a>
                        <a href="/Project/logout.php" class="btn-action">Logout</a>
                    <?php elseif($userRole==='moderator'):?>
                        <a href="/Project/views/viewProfile.php" class="btn-action">My profile</a>
                        <a href="/Project/logout.php" class="btn-action">Logout</a>
                    <?php endif;?>
                </div>
            </div>
            <div class="hero-banner">
                <h1>Media Libary</h1>
                <p>Browse and download your favourite movies, series, games or softwares without the hassle of registering!</p>
            </div>
            <h2 class="content-heading">Contents</h2>
        </main>
    </div>
</body>
</html>