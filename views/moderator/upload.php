<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'moderator') {
    header("Location: ../../login.php");
    exit;
}

include("../../config/db.php");

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Midnight Media - Upload Content</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #00bfff; }
        .error-msg { color: #ff4444; font-size: 0.85rem; margin-top: 4px; display: block; }
        select { width: 300px; padding: 10px; border: none; border-radius: 5px;
                 background: #222; color: white; }
        .back-link { display: inline-block; margin-bottom: 15px; }
    </style>
</head>

<body>

<h1>Midnight Media - Moderator Panel</h1>
<a class="back-link" href="dashboard.php">← Dashboard</a>

<h2>Upload New Content</h2>

<form id="uploadForm" action="../../controllers/upload_content.php" method="POST" enctype="multipart/form-data" novalidate>

    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" placeholder="Enter Title">
        <span class="error-msg" id="titleErr"></span>
    </div>

    <div class="form-group">
        <label for="description">Description *</label>
        <textarea id="description" name="description" placeholder="Enter Description" rows="4"></textarea>
        <span class="error-msg" id="descErr"></span>
    </div>

    <div class="form-group">
        <label for="category_id">Category *</label>
        <select id="category_id" name="category_id">
            <option value="">— Select Category —</option>
            <?php while ($cat = mysqli_fetch_assoc($cats)): ?>
                <option value="<?= (int)$cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                    <?= $cat['parent_id'] ? '(sub)' : '' ?>
                </option>
            <?php endwhile; ?>
        </select>
        <span class="error-msg" id="catErr"></span>
    </div>

    <div class="form-group">
        <label for="media">Media File * <small style="color:#aaa;">(mp4, mkv, avi, zip, rar, exe, iso, pdf — max 5GB)</small></label>
        <input type="file" id="media" name="media" accept=".mp4,.mkv,.avi,.zip,.rar,.exe,.iso,.pdf">
        <span class="error-msg" id="fileErr"></span>
    </div>

    <button type="submit">Upload</button>

</form>

<!-- JS Validation -->
<script>
(function () {
    const ALLOWED = ['mp4','mkv','avi','zip','rar','exe','iso','pdf'];
    const MAX_SIZE = 5 * 1024 * 1024 * 1024;

    const form  = document.getElementById('uploadForm');
    const title = document.getElementById('title');
    const desc  = document.getElementById('description');
    const cat   = document.getElementById('category_id');
    const file  = document.getElementById('media');

    function err(id, msg) { document.getElementById(id).textContent = msg; }
    function clr(id)      { document.getElementById(id).textContent = ''; }

    title.addEventListener('blur', () => {
        clr('titleErr');
        if (!title.value.trim()) err('titleErr', 'Title is required.');
    });
    desc.addEventListener('blur', () => {
        clr('descErr');
        if (!desc.value.trim()) err('descErr', 'Description is required.');
    });
    cat.addEventListener('change', () => {
        clr('catErr');
        if (!cat.value) err('catErr', 'Please select a category.');
    });
    file.addEventListener('change', () => {
        clr('fileErr');
        if (!file.files.length) return;
        const f   = file.files[0];
        const ext = f.name.split('.').pop().toLowerCase();
        if (!ALLOWED.includes(ext)) {
            err('fileErr', 'File type not allowed: ' + ext);
            file.value = '';
            return;
        }
        if (f.size > MAX_SIZE) {
            err('fileErr', 'File exceeds 5 GB limit.');
            file.value = '';
        }
    });

    form.addEventListener('submit', function (e) {
        let ok = true;
        clr('titleErr'); clr('descErr'); clr('catErr'); clr('fileErr');

        if (!title.value.trim()) { err('titleErr', 'Title is required.'); ok = false; }
        if (!desc.value.trim())  { err('descErr',  'Description is required.'); ok = false; }
        if (!cat.value)          { err('catErr',   'Please select a category.'); ok = false; }
        if (!file.files.length) {
            err('fileErr', 'Please select a file.'); ok = false;
        } else {
            const f   = file.files[0];
            const ext = f.name.split('.').pop().toLowerCase();
            if (!ALLOWED.includes(ext)) { err('fileErr', 'File type not allowed.'); ok = false; }
            if (f.size > MAX_SIZE)      { err('fileErr', 'File too large (max 5 GB).'); ok = false; }
        }
        if (!ok) e.preventDefault();
    });
})();
</script>

</body>
</html>
