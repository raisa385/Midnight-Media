<?php
    $role = $_SESSION["userRole"] ?? "member"; //SESSION TO VERIFY ROLE
?>
<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Midnight Media</title>
        <link rel="stylesheet" href="../assets/style2.css">
    </head>
    <body>
        <nav class="navbar">
            <div class="logo-container">
                <img src="/Project/assets/MM_logo.png" alt="Logo" class="logo-image">
                <span class="logo-text">Midnight Media</span>
            </div>
            <div>
                <a href="../controllers/controlHome.php">Home</a>
                <?php if($role == "admin") { ?><a href="../views/admin/dashboard.php">Admin Panel</a>
                <a href="../views/viewProfile.php">Profile</a>
                <a href="../logout.php">Logout</a>
                <?php }else if($role == "moderator"){ ?>
                <a href="../views/moderator/dashboard.php">Moderator Panel</a>
                <a href="../views/viewProfile.php">Profile</a>
                <a href="../logout.php">Logout</a>
                <?php }else{ ?><a href="../views/viewLogin.php">Admin/Moderator Login</a><?php } ?>
            </div>
        </nav>

        <h2>Categories</h2>
            <p>
                <a href="../controllers/controlHome.php" class="<?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $cat){ 
                    $current_parent = ($category_id > 0) ? (getCategoryParentId($conn, $category_id) ?? $category_id) : 0;
                    $active = ($current_parent == $cat["id"]) ? 'active' : '';
                ?>
                    <a href="../controllers/controlHome.php?category=<?php echo $cat["id"]; ?>" class="<?php echo $active; ?>">
                        <?php echo htmlspecialchars($cat["name"]); ?>
                    </a>
                <?php } ?>
            </p>

            <?php if(count($sub_categories) > 0){?>
                <h3>Sub Categories</h3>
                <p>
                    <?php foreach ($sub_categories as $sub) { 
                        $sub_active = ($category_id == $sub["id"]) ? 'active' : '';
                    ?>
                        <a href="../controllers/controlHome.php?category=<?php echo $sub["id"]; ?>" class="<?php echo $sub_active; ?>">
                            <?php echo htmlspecialchars($sub["name"]); ?>
                        </a>
                    <?php } ?>
                </p>
            <?php } ?>

        <form method="GET" action="../views/home.php">
            <input type="text" name="search" id="searchBox" placeholder="search...">
            <button type="submit">Search</button>
        </form>

        <h2>Contents</h2>
        <div id="contentList">
            <?php if (count($contents) == 0) {?>
                <p>No content found.</p>
            <?php } ?>

            <?php foreach ($contents as $content) { ?>
                <div class="box">
                    <h3><?php echo htmlspecialchars($content["title"]); ?></h3>
                    <p><?php echo htmlspecialchars($content["description"]); ?></p>
                    <p>Category: <?php echo htmlspecialchars($content["category_name"] ?? ""); ?></p>
                    <p>Downloads: <?php echo htmlspecialchars($content["download_count"]); ?></p>
                    <a href="../controllers/downloadDummy.php?id=<?php echo $content["id"]; ?>">Download</a>
                </div>
            <?php } ?>
        </div>
        <div>
            <a href="../views/home.php">Request Content</a>
        </div>
    </body>
</html>
