<?php
function checkEmail($conn, $email){
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->num_rows;
    $stmt->close();
    return $rows;
}

function registerUser($conn, $name, $email, $password, $userRole){
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, userRole) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password_hash, $userRole);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
?>