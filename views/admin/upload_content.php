<?php
// views/admin/upload_content.php
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
            <a href="?page=auth&action=logout" class="nav-link logout">🚪 Logout</a>
        </nav>
    </div>

    <main class="admin-main">
        <div class="page-header">
            <h1>Upload Content</h1>
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
            <!-- enctype="multipart/form-data" is REQUIRED for file uploads -->
            <form method="POST" action="?page=admin&action=upload"
                  enctype="multipart/form-data" id="upload-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?= htmlspecialchars($old['title']) ?>"
                           class="form-input" placeholder="e.g. The Dark Knight (2008) [1080p]" required>
                    <span class="field-error" id="err-title"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-input form-textarea"
                              rows="4" placeholder="Brief description of the content..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-input" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($old['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(($cat['parent_name'] ? $cat['parent_name'] . ' → ' : '') . $cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="field-error" id="err-category"></span>
                </div>

                <div class="form-group">
                    <label for="content_file">File <span class="required">*</span></label>
                    <input type="file" id="content_file" name="content_file"
                           class="form-input file-input"
                           accept=".mp4,.mkv,.avi,.zip,.rar,.exe,.iso,.pdf" required>
                    <small class="form-hint">Allowed: mp4, mkv, avi, zip, rar, exe, iso, pdf. Max 5 GB.</small>
                    <span class="field-error" id="err-file"></span>

                    <!-- File preview info (shown by JS after selection) -->
                    <div id="file-info" class="file-info" style="display:none"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Upload Content</button>
                    <a href="?page=admin&action=contents" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
// Show file name and size when user picks a file
document.getElementById('content_file').addEventListener('change', function() {
    const info = document.getElementById('file-info');
    if (this.files.length > 0) {
        const file = this.files[0];
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        info.textContent = `Selected: ${file.name} (${sizeMB} MB)`;
        info.style.display = 'block';
    }
});

// JS validation before submit
document.getElementById('upload-form').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

    const title    = document.getElementById('title').value.trim();
    const category = document.getElementById('category_id').value;
    const fileInput = document.getElementById('content_file');

    if (title.length < 1) {
        document.getElementById('err-title').textContent = 'Title is required.';
        valid = false;
    }
    if (!category) {
        document.getElementById('err-category').textContent = 'Please select a category.';
        valid = false;
    }
    if (fileInput.files.length === 0) {
        document.getElementById('err-file').textContent = 'Please select a file.';
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    } else {
        // Show loading state on submit button
        document.getElementById('submit-btn').textContent = 'Uploading... Please wait';
        document.getElementById('submit-btn').disabled = true;
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
