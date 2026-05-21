<?php
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../controllers/controlHome.php";
include_once __DIR__ . "/../models/modelHome.php";
include_once __DIR__ . "/../controllers/controlHighlights.php";


$role = $_SESSION["userRole"] ?? "member"; //SESSION TO VERIFY ROLE
$selected_parent_id = null;
if (isset($_GET["category"]) && $category_id > 0) {
    $selected_parent_id = getCategoryParentId($conn, $category_id);
    if ($selected_parent_id === null) {
        $contents = getContentsByParent($conn, $category_id);
        if (isset($_GET["search"]) && !empty($search)) {
            $contents = array_filter($contents, function ($content) use ($search) {
                return stripos($content["title"], $search) !== false || stripos($content["description"], $search) !== false;
            });
        }
    } else {
        $sub_categories = getSubCats($conn, $selected_parent_id);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Midnight Media</title>
    <link rel="stylesheet" href="../assets/style1.css">
</head>

<body style="width:80%; margin:0 auto">
    <?php include_once __DIR__ . "/viewNav.php"; ?>
    <h2>Categories</h2>
    <p>
        <a href="../controllers/controlHome.php" class="<?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All</a>
        <?php foreach ($categories as $cat) {
            $current_parent = (isset($_GET['category']) && $category_id > 0) ? ($selected_parent_id ?? $category_id) : 0;
            $active = ($current_parent == $cat["id"]) ? 'active' : '';
            ?>
            <a href="../controllers/controlHome.php?category=<?php echo $cat["id"]; ?>" class="<?php echo $active; ?>">
                <?php echo htmlspecialchars($cat["name"]); ?>
            </a>
        <?php } ?>
    </p>

    <?php if (count($sub_categories) > 0) { ?>
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

    <form method="GET" action="../controllers/controlHome.php">
        <input type="text" name="search" id="searchBox" placeholder="Search by title or description"
            value="<?php echo htmlspecialchars($search); ?>">
        <?php if (isset($_GET["category"])) { ?><input type="hidden" name="category"
                value="<?php echo $category_id; ?>"><?php } ?>
        <button type="submit">Search</button>
    </form>
    <style>
        .H {
            display: flex;
            flex-direction: row;
            gap: 10px;
            height: auto;
            margin: 0 auto;
        }

        .H2 { /**/
            width: 70% !important;
        }

        .H1 {/**/
            width: 30% !important;
        }

        .highlight-item{
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            background-color: white;
        }
    </style>
    <div class="H">
        <div class="H2" id="contentList">
            <h2>Contents</h2>
            <?php if (count($contents) == 0) { ?>
                <p>No content found.</p>
            <?php } ?>

            <?php foreach ($contents as $content) { ?>
                <div class="box">
                    <h3><?php echo htmlspecialchars($content["title"]); ?></h3>
                    <p><?php echo htmlspecialchars($content["description"]); ?></p>
                    <p>Category: <?php echo htmlspecialchars($content["category_name"] ?? ""); ?></p>
                    <p id="downloadCount-<?php echo $content['id']; ?>">Downloads: <?php echo htmlspecialchars($content["download_count"]); ?></p>
                    <button id="downloadBtn" onclick="updateCount(<?php echo $content['id']; ?>)">Download</button>
                </div>
            <?php } ?>
        </div>
        <div class="H1">
            <?php echo $divPrint; ?>
        </div>
    </div>
    <br>
    <div>
        <a href="../views/home.php">Request Content</a>
    </div>
    <script>
        function updateCount(contentId) {
            fetch('../controllers/controlDownloadCount.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded',},
                body: 'content_id=' + contentId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('downloadCount-'+contentId).textContent='Downloads: '+data.download_count;
                }
            });
        }
    </script>
</body>

</html>
