<?php
    include_once __DIR__. "/../config/database.php";
    function getTopCats($conn){
        $stmt = $conn->prepare("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }   

    function getHighlights($conn) {
        $stmt = $conn->prepare("SELECT * FROM contents  ORDER BY download_count DESC, uploaded_at DESC LIMIT 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    function getSubCats($conn, $parent_id){
        $stmt = $conn->prepare("SELECT id, name FROM categories WHERE parent_id = ? ORDER BY name");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    function getAllCats($conn) {
        $stmt = $conn->prepare("SELECT id, name, parent_id FROM categories ORDER BY parent_id, name");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
    function searchContents($conn, $search_term, $category_id = 0) {
        $like_term = '%' . $search_term . '%';
        if($category_id == 0){
            $stmt = $conn->prepare("SELECT * FROM contents WHERE title LIKE ? OR description LIKE ? ORDER BY uploaded_at DESC");
            $stmt->bind_param("ss", $like_term, $like_term);
        } else {
        $stmt = $conn->prepare("SELECT * FROM contents WHERE (title LIKE ? OR description LIKE ?) AND category_id = ? ORDER BY uploaded_at DESC");
        $stmt->bind_param("ssi", $like_term, $like_term, $category_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
    function getAllContents($conn) {
        $stmt = $conn->prepare("SELECT * FROM contents ORDER BY uploaded_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
    function getContentsByCategory($conn, $category_id) {
        $stmt = $conn->prepare("SELECT * FROM contents WHERE category_id = ? ORDER BY uploaded_at DESC");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }

    function getCategoryParentId($conn, $category_id) {
        $stmt = $conn->prepare("SELECT parent_id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? $row['parent_id'] : null;
    }

    function getContentsByParent($conn, $parent_id) {
        $stmt = $conn->prepare("SELECT id FROM categories WHERE parent_id = ?");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $sub_cats = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $ids = array_column($sub_cats, 'id');
        $ids[] = $parent_id;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $stmt = $conn->prepare("SELECT id, title, description, file_path, category_id, download_count, uploaded_at FROM contents WHERE category_id IN ($placeholders) ORDER BY uploaded_at DESC");
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
?>
