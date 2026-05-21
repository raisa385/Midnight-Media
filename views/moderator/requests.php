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

// Status filter
$statusFilter = $_GET['status'] ?? '';
$allowed      = ['', 'pending', 'fulfilled', 'rejected'];
if (!in_array($statusFilter, $allowed)) $statusFilter = '';

if ($statusFilter) {
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM content_requests WHERE request_status=? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $statusFilter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn,
        "SELECT * FROM content_requests ORDER BY created_at DESC");
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Midnight Media - Content Requests</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .filter-tabs { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
        .filter-tabs a { padding:7px 14px; border-radius:5px; background:#222;
                         color:white; text-decoration:none; font-size:0.85rem; }
        .filter-tabs a.active { background:#00bfff; color:black; }
        .filter-tabs a:hover  { background:#00bfff; color:black; }

        .badge { display:inline-block; padding:3px 10px; border-radius:12px;
                 font-size:0.78rem; font-weight:600; text-transform:capitalize; }
        .badge-pending   { background:rgba(255,200,0,0.15);  color:#e09a30; }
        .badge-fulfilled { background:rgba(61,189,122,0.15); color:#3dbd7a; }
        .badge-rejected  { background:rgba(224,80,80,0.15);  color:#e05050; }

        .act-btn { border:none; padding:5px 11px; border-radius:5px;
                   cursor:pointer; font-size:0.82rem; margin-right:4px; }
        .act-btn:disabled { opacity:0.5; cursor:not-allowed; }
        .btn-fulfill { background:#1a5c38; color:#3dbd7a; }
        .btn-reject  { background:#5c1a1a; color:#e05050; }
        .btn-reset   { background:#333;    color:#aaa;    }

        #toast { position:fixed; bottom:20px; right:20px; padding:10px 18px;
                 border-radius:6px; font-size:0.9rem; display:none; z-index:999; }
        .toast-ok  { background:#1a5c38; color:#3dbd7a; }
        .toast-err { background:#5c1a1a; color:#e05050; }

        .back-link { display:inline-block; margin-bottom:15px; }
    </style>
</head>

<body>

<h1>Midnight Media - Moderator Panel</h1>
<a class="back-link" href="dashboard.php">← Dashboard</a>

<h2>Content Requests</h2>

<!-- Status Filter Tabs -->
<div class="filter-tabs">
    <a href="requests.php"                    class="<?= !$statusFilter ? 'active' : '' ?>">All</a>
    <a href="requests.php?status=pending"     class="<?= $statusFilter==='pending'   ? 'active' : '' ?>">⏳ Pending</a>
    <a href="requests.php?status=fulfilled"   class="<?= $statusFilter==='fulfilled' ? 'active' : '' ?>">✅ Fulfilled</a>
    <a href="requests.php?status=rejected"    class="<?= $statusFilter==='rejected'  ? 'active' : '' ?>">❌ Rejected</a>
</div>

<?php if (mysqli_num_rows($result) == 0): ?>
    <p style="color:#aaa;">No requests found.</p>
<?php else: ?>

<table>
<tr>
    <th>#</th>
    <th>Content Title</th>
    <th>Category</th>
    <th>Message</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
$i = 1;
while ($row = mysqli_fetch_assoc($result)):
    $status = htmlspecialchars($row['request_status']);
?>
<tr id="req-<?= (int)$row['id'] ?>">
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($row['content_title']) ?></td>
    <td><?= htmlspecialchars($row['category_requested'] ?? '—') ?></td>
    <td style="max-width:200px;"><?= htmlspecialchars($row['request_message'] ?? '—') ?></td>
    <td><?= htmlspecialchars(date('d M Y', strtotime($row['created_at']))) ?></td>
    <td>
        <span class="badge badge-<?= $status ?>" id="badge-<?= (int)$row['id'] ?>">
            <?= ucfirst($status) ?>
        </span>
    </td>
    <td id="actions-<?= (int)$row['id'] ?>">
        <?php if ($status !== 'fulfilled'): ?>
        <button class="act-btn btn-fulfill" onclick="updateStatus(<?= (int)$row['id'] ?>, 'fulfilled')">✅ Fulfill</button>
        <?php endif; ?>
        <?php if ($status !== 'rejected'): ?>
        <button class="act-btn btn-reject"  onclick="updateStatus(<?= (int)$row['id'] ?>, 'rejected')">❌ Reject</button>
        <?php endif; ?>
        <?php if ($status !== 'pending'): ?>
        <button class="act-btn btn-reset"   onclick="updateStatus(<?= (int)$row['id'] ?>, 'pending')">↩ Reset</button>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>
<?php endif; ?>

<div id="toast"></div>

<!-- AJAX Status Update -->
<script>
const CSRF = <?= json_encode($csrf) ?>;

function showToast(msg, ok) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = ok ? 'toast-ok' : 'toast-err';
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2800);
}

function buildButtons(id, status) {
    let h = '';
    if (status !== 'fulfilled') h += `<button class="act-btn btn-fulfill" onclick="updateStatus(${id},'fulfilled')">✅ Fulfill</button> `;
    if (status !== 'rejected')  h += `<button class="act-btn btn-reject"  onclick="updateStatus(${id},'rejected')">❌ Reject</button> `;
    if (status !== 'pending')   h += `<button class="act-btn btn-reset"   onclick="updateStatus(${id},'pending')">↩ Reset</button>`;
    return h;
}

function updateStatus(id, status) {
    // Disable all buttons in the row while request is running
    document.querySelectorAll('#actions-' + id + ' button')
        .forEach(b => b.disabled = true);

    fetch('../../api/update_request_ajax.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ csrf_token: CSRF, request_id: id, status: status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const labels = { pending:'Pending', fulfilled:'Fulfilled', rejected:'Rejected' };
            document.getElementById('badge-' + id).textContent = labels[status];
            document.getElementById('badge-' + id).className = 'badge badge-' + status;
            document.getElementById('actions-' + id).innerHTML = buildButtons(id, status);
        
            showToast('Status updated to ' + labels[status], true);
        } else {
            showToast(data.message || 'Failed to update.', false);
            document.querySelectorAll('#actions-' + id + ' button')
                .forEach(b => b.disabled = false);
        }
    })
    .catch(() => {
        showToast('Network error. Try again.', false);
        document.querySelectorAll('#actions-' + id + ' button')
            .forEach(b => b.disabled = false);
    });
}
</script>

</body>
</html>
