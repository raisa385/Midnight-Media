<?php

function getUserById($conn, $id) {
    $sql = $conn->prepare("SELECT id, name, email, userRole, profilePic FROM users WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $result = $sql->get_result();
    $user = $result->fetch_assoc();
    $sql->close();
    return $user;
}

function updateProfilePic($conn, $id, $filename) {
    $sql = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
    $sql->bind_param("si", $filename, $id);
    $result = $sql->execute();
    $sql->close();
    return $result;
}