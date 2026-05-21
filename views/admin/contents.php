<?php
// views/admin/contents.php
?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="sidebar-brand">🌙 Midnight Media</div>
        <nav class="sidebar-nav">
            <a href="?page=admin&action=dashboard" class="nav-link">📊 Dashboard</a>
            <a href="?page=admin&action=moderators" class="nav-link">👥 Moderators</a>
            <a href="?page=admin&action=contents" class="nav-link active">🎬 Contents</a>
            <a href="?page=admin&action=requests" class="nav-link">📬 Requests</a>
            <button class="nav-link theme-toggle" onclick="toggleTheme()" id="theme-btn">
                ☀️ Light Mode
            </button>
            <a href="?page=auth&action=logout" class="nav-link logout">🚪 Logout</a>
        </nav>
    </div>

    <main class="admin-main">
        <div class="page-header">
            <h1>Manage Contents</h1>
            <a href="?page=admin&action=upload" class="btn btn-primary">+ Upload Content</a>
        </div>

        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Search bar (client-side filter) -->
        <div class="search-bar">
            <input type="text" id="content-search" class="form-input"
                   placeholder="Search by title or category...">
        </div>

        <?php if (empty($contents)): ?>
            <div class="card">
                <p class="empty-msg">No content uploaded yet. <a href="?page=admin&action=upload">Upload now</a>.</p>
            </div>
        <?php else: ?>
        <div class="card table-card">
            <table class="data-table" id="contents-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Uploaded By</th>
                        <th>Downloads</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contents as $item): ?>
                    <tr id="content-row-<?= $item['id'] ?>" class="content-row">
                        <td><?= $item['id'] ?></td>
                        <td class="content-title"><?= htmlspecialchars($item['title']) ?></td>
                        <td class="content-category"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                        <td><?= htmlspecialchars($item['uploader_name'] ?? 'Unknown') ?></td>
                        <td><?= number_format($item['download_count']) ?></td>
                        <td><?= date('d M Y', strtotime($item['uploaded_at'])) ?></td>
                        <td class="action-cell">
                            <a href="?page=admin&action=edit&id=<?= $item['id'] ?>"
                               class="btn btn-secondary btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm"
                                    onclick="deleteContent(<?= $item['id'] ?>, '<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>')">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
const CSRF_TOKEN = '<?= htmlspecialchars($csrf) ?>';

// Client-side live search filter
document.getElementById('content-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.content-row').forEach(row => {
        const title    = row.querySelector('.content-title').textContent.toLowerCase();
        const category = row.querySelector('.content-category').textContent.toLowerCase();
        row.style.display = (title.includes(query) || category.includes(query)) ? '' : 'none';
    });
});

// AJAX delete content
function deleteContent(id, title) {
    if (!confirm(`Delete "${title}"? This will permanently remove the file.`)) return;

    fetch('/Project/api/admin.php?action=delete_content', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `content_id=${id}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('content-row-' + id);
            if (row) row.remove();
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(() => showToast('Network error. Please try again.', 'error'));
}

function showToast(msg, type) {
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
