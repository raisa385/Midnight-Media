<?php
// views/admin/requests.php
?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="sidebar-brand">🌙 Midnight Media</div>
        <nav class="sidebar-nav">
            <a href="?page=admin&action=dashboard" class="nav-link">📊 Dashboard</a>
            <a href="?page=admin&action=moderators" class="nav-link">👥 Moderators</a>
            <a href="?page=admin&action=contents" class="nav-link">🎬 Contents</a>
            <a href="?page=admin&action=requests" class="nav-link active">📬 Requests</a>
            <button class="nav-link theme-toggle" onclick="toggleTheme()" id="theme-btn">
                ☀️ Light Mode
            </button>
            <a href="?page=auth&action=logout" class="nav-link logout">🚪 Logout</a>
        </nav>
    </div>

    <main class="admin-main">
        <div class="page-header">
            <h1>Content Requests</h1>
        </div>

        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($requests)): ?>
            <div class="card">
                <p class="empty-msg">No content requests yet.</p>
            </div>
        <?php else: ?>
        <div class="card table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Requested Title</th>
                        <th>Category</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= $req['id'] ?></td>
                        <td><?= htmlspecialchars($req['content_title']) ?></td>
                        <td><?= htmlspecialchars($req['category_requested'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($req['request_message'] ?? '—') ?></td>
                        <td>
                            <span class="badge badge--<?= $req['request_status'] ?>">
                                <?= ucfirst($req['request_status']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($req['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
