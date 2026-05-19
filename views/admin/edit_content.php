<?php
// views/admin/edit_content.php
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
            <h1>Edit Content</h1>
            <a href="?page=admin&action=contents" class="btn btn-secondary">← Back</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card form-card">
            <form method="POST" action="?page=admin&action=edit"
                  enctype="multipart/form-data" id="edit-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="content_id" value="<?= $content['id'] ?>">

                <div class="form-group">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?= htmlspecialchars($content['title']) ?>"
                           class="form-input" required>
                    <span class="field-error" id="err-title"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                              class="form-input form-textarea" rows="4"><?= htmlspecialchars($content['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-input" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($content['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($cat['parent_name'] ? $cat['parent_name'] . ' → ' : '') . $cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="field-error" id="err-category"></span>
                </div>

                <div class="form-group">
                    <label>Current File</label>
                    <div class="current-file">
                        📎 <?= htmlspecialchars(basename($content['file_path'])) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content_file">Replace File (optional)</label>
                    <input type="file" id="content_file" name="content_file"
                           class="form-input file-input"
                           accept=".mp4,.mkv,.avi,.zip,.rar,.exe,.iso,.pdf">
                    <small class="form-hint">Leave empty to keep the current file.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="?page=admin&action=contents" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.getElementById('edit-form').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

    const title    = document.getElementById('title').value.trim();
    const category = document.getElementById('category_id').value;

    if (title.length < 1) {
        document.getElementById('err-title').textContent = 'Title is required.';
        valid = false;
    }
    if (!category) {
        document.getElementById('err-category').textContent = 'Please select a category.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
