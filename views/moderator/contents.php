<?php

session_start();

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'moderator'){
    header("Location: ../viewLogin.php");
    exit();
}

include("../../config/db.php");

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Flash message (delete 
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// ── Search
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = mysqli_prepare($conn,
        "SELECT c.*, cat.name AS category_name
         FROM contents c
         LEFT JOIN categories cat ON c.category_id = cat.id
         WHERE c.title LIKE ? OR c.description LIKE ?
         ORDER BY c.uploaded_at DESC");
    $like = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn,
        "SELECT c.*, cat.name AS category_name
         FROM contents c
         LEFT JOIN categories cat ON c.category_id = cat.id
         ORDER BY c.uploaded_at DESC");
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Midnight Media - All Contents</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .flash-ok  { background:#1a3a2a; color:#3dbd7a; padding:10px; border-radius:5px; margin-bottom:15px; }
        .flash-err { background:#3a1a1a; color:#e05050; padding:10px; border-radius:5px; margin-bottom:15px; }
        .btn-del { background:#cc3333; color:white; border:none; padding:6px 12px;
                   border-radius:5px; cursor:pointer; font-size:0.85rem; }
        .btn-del:hover { background:#ff4444; }
        .back-link { display:inline-block; margin-bottom:15px; }
        .search-form { display:flex; gap:10px; margin-bottom:15px; align-items:center; }
        .search-form input { width:260px; }
    </style>
</head>

<body>

<h1>Midnight Media - Moderator Panel</h1>
<a class="back-link" href="dashboard.php">← Dashboard</a>

<h2>All Uploaded Contents</h2>

<?php if ($flash): ?>
    <div class="flash-ok"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Search Form -->
<form method="GET" class="search-form" id="searchForm">
    <input type="text" id="searchInput" name="search"
           value="<?= htmlspecialchars($search) ?>"
           placeholder="Search by title or description...">
    <button type="submit">Search</button>
    <?php if ($search): ?>
        <a href="contents.php" style="padding:8px 14px;">✕ Clear</a>
    <?php endif; ?>
</form>

<table>

<tr>
    <th>#</th>
    <th>Title</th>
    <th>Description</th>
    <th>Category</th>
    <th>Downloads</th>
    <th>Uploaded</th>
    <th>Action</th>
</tr>

<?php
$i = 1;
while ($row = mysqli_fetch_assoc($result)):
?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td style="max-width:220px;"><?= htmlspecialchars($row['description']) ?></td>
    <td><?= htmlspecialchars($row['category_name'] ?? '—') ?></td>
    <td><?= (int)$row['download_count'] ?></td>
    <td><?= htmlspecialchars(date('d M Y', strtotime($row['uploaded_at']))) ?></td>
    <td>
        <!-- DELETE: POST form + CSRF -->
        <form method="POST" action="../../controllers/delete_content.php"
              class="del-form"
              data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>">
            <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="content_id"  value="<?= (int)$row['id'] ?>">
            <button type="submit" class="btn-del">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>

</table>

<!-- JS: delete confirm + search trim -->
<script>
document.querySelectorAll('.del-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!confirm('Delete "' + form.dataset.title + '"?\nThis cannot be undone.')) {
            e.preventDefault();
        }
    });
});
document.getElementById('searchForm').addEventListener('submit', function() {
    document.getElementById('searchInput').value =
        document.getElementById('searchInput').value.trim();
});
</script>

</body>
</html>
