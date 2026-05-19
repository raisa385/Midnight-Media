<?php
    function getTopCats($conn){
        $stmt=$conn->prepare("SELECT id,name FROM categories WHERE parent_id IS NULL");
        $stmt->execute();
        $res=$stmt->get_result();
        $data=$res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
?>