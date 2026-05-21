<?php
// views/admin/moderators.php
?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="sidebar-brand">🌙 Midnight Media</div>
        <nav class="sidebar-nav">
            <a href="?page=admin&action=dashboard" class="nav-link">📊 Dashboard</a>
            <a href="?page=admin&action=moderators" class="nav-link active">👥 Moderators</a>
            <a href="?page=admin&action=contents" class="nav-link">🎬 Contents</a>
            <a href="?page=admin&action=requests" class="nav-link">📬 Requests</a>
            <button class="nav-link theme-toggle" onclick="toggleTheme()" id="theme-btn">
                ☀️ Light Mode
            </button>
            <a href="?page=auth&action=logout" class="nav-link logout">🚪 Logout</a>
        </nav>
    </div>

    <main class="admin-main">
        <div class="page-header">
            <h1>Manage Moderators</h1>
            <a href="?page=admin&action=add_moderator" class="btn btn-primary">+ Add Moderator</a>
        </div>

        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($moderators)): ?>
            <div class="card">
                <p class="empty-msg">No moderators yet. <a href="?page=admin&action=add_moderator">Add one</a>.</p>
            </div>
        <?php else: ?>
        <div class="card">
            <table class="data-table" id="moderators-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($moderators as $mod): ?>
                    <tr id="mod-row-<?= $mod['id'] ?>">
                        <td><?= $mod['id'] ?></td>
                        <td>
                            <div class="user-cell">
                                <img src="<?= htmlspecialchars($mod['profilePic'] ?? 'public/assets/default-avatar.png') ?>"
                                     class="avatar-sm" alt="avatar">
                                <?= htmlspecialchars($mod['name']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($mod['email']) ?></td>
                        <td><?= date('d M Y', strtotime($mod['created_at'])) ?></td>
                        <td>
                            <!-- AJAX delete button -->
                            <button class="btn btn-danger btn-sm"
                                    onclick="deleteModerator(<?= $mod['id'] ?>, '<?= htmlspecialchars($mod['name'], ENT_QUOTES) ?>')">
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

function deleteModerator(id, name) {
    if (!confirm(`Delete moderator "${name}"? Their content will be reassigned to admin.`)) return;

    fetch('/Project/api/admin.php?action=delete_moderator', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `moderator_id=${id}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Remove row from table without page reload
            const row = document.getElementById('mod-row-' + id);
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
