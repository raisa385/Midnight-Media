<?php

// Handles all database operations related to media contents.

require_once __DIR__ . '/../config/db.php';

class Content {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    
    public function getAllWithDetails(): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS uploader_name, cat.name AS category_name
             FROM contents c
             LEFT JOIN users u ON c.uploader_id = u.id
             LEFT JOIN categories cat ON c.category_id = cat.id
             ORDER BY c.uploaded_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT c.*, cat.name AS category_name
             FROM contents c
             LEFT JOIN categories cat ON c.category_id = cat.id
             WHERE c.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    
    public function create(string $title, string $description, string $filePath, int $categoryId, int $uploaderId): int {
        $stmt = $this->db->prepare(
            "INSERT INTO contents (title, description, file_path, category_id, uploader_id)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $description, $filePath, $categoryId, $uploaderId]);
        return (int) $this->db->lastInsertId();
    }

    // Update existing content record
    public function update(int $id, string $title, string $description, int $categoryId, ?string $filePath = null): bool {
        if ($filePath) {
            // If a new file was uploaded, update file_path too
            $stmt = $this->db->prepare(
                "UPDATE contents SET title=?, description=?, category_id=?, file_path=? WHERE id=?"
            );
            return $stmt->execute([$title, $description, $categoryId, $filePath, $id]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE contents SET title=?, description=?, category_id=? WHERE id=?"
            );
            return $stmt->execute([$title, $description, $categoryId, $id]);
        }
    }

    // Delete content record 
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM contents WHERE id=?");
        return $stmt->execute([$id]);
    }

    // Get file_path before deleting 
    public function getFilePath(int $id): string|false {
        $stmt = $this->db->prepare("SELECT file_path FROM contents WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $row['file_path'] : false;
    }

    // Count total contents 
    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM contents");
        return (int) $stmt->fetchColumn();
    }

    // Count pending content requests 
    public function countPendingRequests(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM content_requests WHERE request_status='pending'");
        return (int) $stmt->fetchColumn();
    }

    // Count all categories 
    public function countCategories(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
        return (int) $stmt->fetchColumn();
    }

    // Get most downloaded contents (for dashboard)
    public function getTopDownloaded(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT c.title, c.download_count, cat.name AS category_name
             FROM contents c
             LEFT JOIN categories cat ON c.category_id = cat.id
             ORDER BY c.download_count DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
