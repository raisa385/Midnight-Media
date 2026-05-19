<?php

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/User.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    
    case 'stats':
        $contentModel = new Content();
        $userModel    = new User();
        echo json_encode([
            'success' => true,
            'data'    => [
                'total_contents'   => $contentModel->countAll(),
                'total_moderators' => $userModel->countModerators(),
                'pending_requests' => $contentModel->countPendingRequests(),
            ]
        ]);
        break;

    
    case 'delete_content':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }

        
        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF token mismatch']);
            break;
        }

        $id = filter_input(INPUT_POST, 'content_id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            break;
        }

        $contentModel = new Content();
        $filePath     = $contentModel->getFilePath($id);

        if ($contentModel->delete($id)) {
            if ($filePath) {
                $fullPath = __DIR__ . '/../' . $filePath;
                if (file_exists($fullPath)) unlink($fullPath);
            }
            echo json_encode(['success' => true, 'message' => 'Content deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed.']);
        }
        break;

    
    case 'delete_moderator':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF token mismatch']);
            break;
        }

        $id = filter_input(INPUT_POST, 'moderator_id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            break;
        }

        $userModel = new User();
        if ($userModel->deleteModerator($id)) {
            echo json_encode(['success' => true, 'message' => 'Moderator deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed.']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
