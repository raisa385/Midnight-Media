<?php

// All admin business logic lives here.

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/Category.php';

class AdminController {

   
    private function requireAdmin(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['userRole'] !== 'admin') {
            header('Location: ?page=auth&action=login');
            exit;
        }
    }

    // ─── CSRF helpers ────
    private function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // ─── Flash message helpers ────────────────────────────────────────────────
    private function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function getFlash(): array {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    
    private function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================
    public function dashboard(): void {
        $this->requireAdmin();

        $contentModel  = new Content();
        $userModel     = new User();

        $stats = [
            'total_contents'   => $contentModel->countAll(),
            'total_categories' => $contentModel->countCategories(),
            'total_moderators' => $userModel->countModerators(),
            'pending_requests' => $contentModel->countPendingRequests(),
        ];
        $topDownloaded = $contentModel->getTopDownloaded(5);
        $flash         = $this->getFlash();

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // =========================================================================
    // MODERATOR MANAGEMENT
    // =========================================================================

    public function listModerators(): void {
        $this->requireAdmin();

        $userModel  = new User();
        $moderators = $userModel->getAllModerators();
        $flash      = $this->getFlash();
        $csrf       = $this->generateCsrfToken();

        require __DIR__ . '/../views/admin/moderators.php';
    }

    public function addModerator(): void {
        $this->requireAdmin();

        $errors = [];
        $old    = ['name' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid form submission. Please try again.';
            } else {
                
                $name     = $this->sanitize($_POST['name'] ?? '');
                $email    = $this->sanitize($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';          // passwords not sanitized (hashed)
                $confirm  = $_POST['confirm_password'] ?? '';
                $old      = ['name' => $name, 'email' => $email];

                // Server-side validation
                if (empty($name))  $errors[] = 'Name is required.';
                if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
                if (empty($email)) $errors[] = 'Email is required.';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
                if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
                if ($password !== $confirm) $errors[] = 'Passwords do not match.';

                if (empty($errors)) {
                    $userModel = new User();
                    // Check email uniqueness
                    if ($userModel->findByEmail($email)) {
                        $errors[] = 'A user with this email already exists.';
                    } else {
                        $userModel->createModerator($name, $email, $password);
                        $this->setFlash('success', "Moderator '{$name}' created successfully.");
                        header('Location: ?page=admin&action=moderators');
                        exit;
                    }
                }
            }
        }

        $csrf = $this->generateCsrfToken();
        require __DIR__ . '/../views/admin/add_moderator.php';
    }

    public function deleteModerator(): void {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=moderators');
            exit;
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request.');
            header('Location: ?page=admin&action=moderators');
            exit;
        }

        $id = filter_input(INPUT_POST, 'moderator_id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->setFlash('error', 'Invalid moderator ID.');
            header('Location: ?page=admin&action=moderators');
            exit;
        }

        $userModel = new User();
        if ($userModel->deleteModerator($id)) {
            $this->setFlash('success', 'Moderator deleted. Their content has been reassigned to admin.');
        } else {
            $this->setFlash('error', 'Could not delete moderator.');
        }

        header('Location: ?page=admin&action=moderators');
        exit;
    }

    // =========================================================================
    // CONTENT MANAGEMENT
    // =========================================================================

    
    private const ALLOWED_EXTENSIONS = ['mp4', 'mkv', 'avi', 'zip', 'rar', 'exe', 'iso', 'pdf'];
    private const ALLOWED_MIMES = [
        'video/mp4', 'video/x-matroska', 'video/x-msvideo',
        'application/zip', 'application/x-rar-compressed',
        'application/vnd.rar',
        'application/x-msdownload', 'application/x-cd-image',
        'application/pdf'
    ];
    private const MAX_FILE_SIZE = 5368709120; 

    public function listContents(): void {
        $this->requireAdmin();

        $contentModel = new Content();
        $contents     = $contentModel->getAllWithDetails();
        $flash        = $this->getFlash();
        $csrf         = $this->generateCsrfToken();

        require __DIR__ . '/../views/admin/contents.php';
    }

    public function uploadContent(): void {
        $this->requireAdmin();

        $errors = [];
        $old    = ['title' => '', 'description' => '', 'category_id' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid form submission.';
            } else {
                $title       = $this->sanitize($_POST['title'] ?? '');
                $description = $this->sanitize($_POST['description'] ?? '');
                $categoryId  = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
                $old         = ['title' => $title, 'description' => $description, 'category_id' => $categoryId];

                // Validate text fields
                if (empty($title))    $errors[] = 'Title is required.';
                if (!$categoryId)     $errors[] = 'Please select a category.';

                // Validate file upload
                if (empty($_FILES['content_file']['name'])) {
                    $errors[] = 'Please select a file to upload.';
                } else {
                    $file      = $_FILES['content_file'];
                    $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $mimeType  = mime_content_type($file['tmp_name']);

                    if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
                        $errors[] = 'File type not allowed. Allowed: ' . implode(', ', self::ALLOWED_EXTENSIONS);
                    }
                    if (!in_array($mimeType, self::ALLOWED_MIMES)) {
                        $errors[] = 'File MIME type not allowed.';
                    }
                    if ($file['size'] > self::MAX_FILE_SIZE) {
                        $errors[] = 'File size exceeds 5 GB limit.';
                    }
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        $errors[] = 'File upload error. Please try again.';
                    }
                }

                if (empty($errors)) {
                    // Generate safe unique filename
                    $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
                    $filename  = $safeTitle . '_' . uniqid() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../public/uploads/contents/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $destination = $uploadDir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $filePath     = 'public/uploads/contents/' . $filename;
                        $contentModel = new Content();
                        $contentModel->create($title, $description, $filePath, $categoryId, $_SESSION['user_id']);

                        $this->setFlash('success', "'{$title}' uploaded successfully.");
                        header('Location: ?page=admin&action=contents');
                        exit;
                    } else {
                        $errors[] = 'Failed to save file. Check folder permissions.';
                    }
                }
            }
        }

        $categoryModel = new Category();
        $categories    = $categoryModel->getAll();
        $csrf          = $this->generateCsrfToken();

        require __DIR__ . '/../views/admin/upload_content.php';
    }

    public function editContent(): void {
        $this->requireAdmin();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
            ?? filter_input(INPUT_POST, 'content_id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->setFlash('error', 'Invalid content ID.');
            header('Location: ?page=admin&action=contents');
            exit;
        }

        $contentModel = new Content();
        $content      = $contentModel->findById($id);

        if (!$content) {
            $this->setFlash('error', 'Content not found.');
            header('Location: ?page=admin&action=contents');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Invalid form submission.';
            } else {
                $title       = $this->sanitize($_POST['title'] ?? '');
                $description = $this->sanitize($_POST['description'] ?? '');
                $categoryId  = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

                if (empty($title))  $errors[] = 'Title is required.';
                if (!$categoryId)   $errors[] = 'Please select a category.';

                $newFilePath = null;

                // Handle optional new file upload
                if (!empty($_FILES['content_file']['name'])) {
                    $file     = $_FILES['content_file'];
                    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $mimeType = mime_content_type($file['tmp_name']);

                    if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
                        $errors[] = 'File type not allowed.';
                    }
                    if (!in_array($mimeType, self::ALLOWED_MIMES)) {
                        $errors[] = 'File MIME type not allowed.';
                    }

                    if (empty($errors)) {
                        $safeTitle   = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
                        $filename    = $safeTitle . '_' . uniqid() . '.' . $ext;
                        $uploadDir   = __DIR__ . '/../public/uploads/contents/';
                        $destination = $uploadDir . $filename;

                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            // Delete old file
                            $oldPath = __DIR__ . '/../' . $content['file_path'];
                            if (file_exists($oldPath)) unlink($oldPath);
                            $newFilePath = 'public/uploads/contents/' . $filename;
                        } else {
                            $errors[] = 'Failed to save new file.';
                        }
                    }
                }

                if (empty($errors)) {
                    $contentModel->update($id, $title, $description, $categoryId, $newFilePath);
                    $this->setFlash('success', "'{$title}' updated successfully.");
                    header('Location: ?page=admin&action=contents');
                    exit;
                }
            }
        }

        $categoryModel = new Category();
        $categories    = $categoryModel->getAll();
        $csrf          = $this->generateCsrfToken();

        require __DIR__ . '/../views/admin/edit_content.php';
    }

    public function deleteContent(): void {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=admin&action=contents');
            exit;
        }

        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request.');
            header('Location: ?page=admin&action=contents');
            exit;
        }

        $id = filter_input(INPUT_POST, 'content_id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->setFlash('error', 'Invalid content ID.');
            header('Location: ?page=admin&action=contents');
            exit;
        }

        $contentModel = new Content();
        $filePath     = $contentModel->getFilePath($id);

        if ($contentModel->delete($id)) {
            // Delete the actual file from server
            if ($filePath) {
                $fullPath = __DIR__ . '/../' . $filePath;
                if (file_exists($fullPath)) unlink($fullPath);
            }
            $this->setFlash('success', 'Content deleted successfully.');
        } else {
            $this->setFlash('error', 'Could not delete content.');
        }

        header('Location: ?page=admin&action=contents');
        exit;
    }

    // =========================================================================
    // VIEW CONTENT REQUESTS 
    // =========================================================================
    public function viewRequests(): void {
        $this->requireAdmin();

        $pdo   = getDB();
        $stmt  = $pdo->query(
            "SELECT * FROM content_requests ORDER BY created_at DESC"
        );
        $requests = $stmt->fetchAll();
        $flash    = $this->getFlash();

        require __DIR__ . '/../views/admin/requests.php';
    }
}
