<?php


require_once __DIR__ . '/../config/db.php';

class Category {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT c.*, p.name AS parent_name
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             ORDER BY c.parent_id ASC, c.name ASC"
        );
        return $stmt->fetchAll();
    }

    
    public function getTopLevel(): array {
        $stmt = $this->db->query(
            "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC"
        );
        return $stmt->fetchAll();
    }
}
