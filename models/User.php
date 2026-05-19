<?php

require_once __DIR__ . '/../config/db.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Get all moderators (for admin to view)
    public function getAllModerators(): array {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, profilePic, created_at
             FROM users WHERE userRole = 'moderator'
             ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Find a user by email (used during login and checking duplicates)
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Find a user by ID
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, userRole, profilePic, created_at FROM users WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Create a new moderator account (admin only)
    public function createModerator(string $name, string $email, string $password): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, userRole) VALUES (?, ?, ?, 'moderator')"
        );
        return $stmt->execute([$name, $email, $hash]);
    }

    // Delete a moderator by ID
    // Also reassigns their uploaded contents to the admin (uploader_id = 1)
    public function deleteModerator(int $id): bool {
        // Reassign content to admin before deleting
        $stmt = $this->db->prepare(
            "UPDATE contents SET uploader_id = 1 WHERE uploader_id = ?"
        );
        $stmt->execute([$id]);

        // Now delete the moderator
        $stmt2 = $this->db->prepare(
            "DELETE FROM users WHERE id = ? AND userRole = 'moderator'"
        );
        return $stmt2->execute([$id]);
    }

    // Count all moderators (for dashboard stats)
    public function countModerators(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE userRole = 'moderator'");
        return (int) $stmt->fetchColumn();
    }
}
