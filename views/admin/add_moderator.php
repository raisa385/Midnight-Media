<?php
// views/admin/add_moderator.php
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
            <a href="?page=auth&action=logout" class="nav-link logout">🚪 Logout</a>
        </nav>
    </div>

    <main class="admin-main">
        <div class="page-header">
            <h1>Add Moderator</h1>
            <a href="?page=admin&action=moderators" class="btn btn-secondary">← Back</a>
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
            <form method="POST" action="?page=admin&action=add_moderator" id="add-mod-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= htmlspecialchars($old['name']) ?>"
                           class="form-input" placeholder="e.g. John Doe" required>
                    <span class="field-error" id="err-name"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($old['email']) ?>"
                           class="form-input" placeholder="e.g. mod@midnight.com" required>
                    <span class="field-error" id="err-email"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password"
                           class="form-input" placeholder="Min 8 characters" required minlength="8">
                    <span class="field-error" id="err-password"></span>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           class="form-input" placeholder="Repeat password" required>
                    <span class="field-error" id="err-confirm"></span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Moderator</button>
                    <a href="?page=admin&action=moderators" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
// Client-side (JS) validation — runs before form is submitted to server
document.getElementById('add-mod-form').addEventListener('submit', function(e) {
    let valid = true;

    // Clear previous errors
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-input').forEach(el => el.classList.remove('input-error'));

    const name     = document.getElementById('name').value.trim();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm  = document.getElementById('confirm_password').value;

    if (name.length < 2) {
        showError('name', 'Name must be at least 2 characters.');
        valid = false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('email', 'Enter a valid email address.');
        valid = false;
    }

    if (password.length < 8) {
        showError('password', 'Password must be at least 8 characters.');
        valid = false;
    }

    if (password !== confirm) {
        showError('confirm', 'Passwords do not match.');
        valid = false;
    }

    if (!valid) e.preventDefault();
});

function showError(field, msg) {
    const errEl = document.getElementById('err-' + field);
    const input = document.getElementById(field === 'confirm' ? 'confirm_password' : field);
    if (errEl) errEl.textContent = msg;
    if (input) input.classList.add('input-error');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
