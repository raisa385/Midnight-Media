<?php
// views/admin/dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['userRole'] ?? '') !== 'admin') {
    header('Location: ../viewLogin.php');
    exit();
}

if (!isset($stats) || !isset($topDownloaded)) {
    require_once __DIR__ . '/../../models/Content.php';
    require_once __DIR__ . '/../../models/User.php';

    $contentModel = new Content();
    $userModel = new User();

    $stats = [
        'total_contents' => $contentModel->countAll(),
        'total_categories' => $contentModel->countCategories(),
        'total_moderators' => $userModel->countModerators(),
        'pending_requests' => $contentModel->countPendingRequests(),
    ];
    $topDownloaded = $contentModel->getTopDownloaded(5);
}

$flash = $flash ?? [];
?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="sidebar-brand">🌙 Midnight Media</div>
        <nav class="sidebar-nav">
            <a href="/Project/index.php?page=admin&action=dashboard" class="nav-link active">📊 Dashboard</a>
            <a href="/Project/index.php?page=admin&action=moderators" class="nav-link">👥 Moderators</a>
            <a href="/Project/index.php?page=admin&action=contents" class="nav-link">🎬 Contents</a>
            <a href="/Project/index.php?page=admin&action=requests" class="nav-link">📬 Requests</a>
            <a href="/Project/controllers/controlHome.php" class="nav-link">Home</a>
            <button class="nav-link theme-toggle" onclick="toggleTheme()" id="theme-btn">☀️ Light Mode
            </button>
            <a href="/Project/logout.php" class="nav-link logout">🚪 Logout</a>
        </nav>
    </div>

    <main class="admin-main">
        <div class="page-header">
            <h1>Admin Dashboard</h1>
            <span class="admin-badge">Logged in as: <?= htmlspecialchars($_SESSION['name']) ?></span>
        </div>

        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="stat-contents"><?= $stats['total_contents'] ?></div>
                <div class="stat-label">Total Contents</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_categories'] ?></div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-moderators"><?= $stats['total_moderators'] ?></div>
                <div class="stat-label">Moderators</div>
            </div>
            <div class="stat-card stat-card--warning">
                <div class="stat-number" id="stat-requests"><?= $stats['pending_requests'] ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <a href="/Project/index.php?page=admin&action=upload" class="btn btn-primary">+ Upload Content</a>
            <a href="/Project/index.php?page=admin&action=add_moderator" class="btn btn-secondary">+ Add Moderator</a>
        </div>

        <!-- Top Downloaded -->
        <div class="card">
            <h2>Top Downloaded</h2>
            <?php if (empty($topDownloaded)): ?>
                <p class="empty-msg">No content uploaded yet.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Downloads</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topDownloaded as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
                            <td><?= number_format($row['download_count']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
