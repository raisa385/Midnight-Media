<?php



session_start();
header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'moderator') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// JSON body parse
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// CSRF check
if (empty($body['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $body['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'CSRF token mismatch.']);
    exit;
}

$id     = filter_var($body['request_id'] ?? 0, FILTER_VALIDATE_INT);
$status = $body['status'] ?? '';
$allowed = ['pending', 'fulfilled', 'rejected'];

if (!$id || !in_array($status, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

include(__DIR__ . '/../config/db.php');


$stmt = mysqli_prepare($conn,
    "UPDATE content_requests SET request_status=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'si', $status, $id);
$ok = mysqli_stmt_execute($stmt);

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Status updated.' : 'Failed to update status.',
]);
exit;
