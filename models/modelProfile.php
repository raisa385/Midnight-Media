<?php
    function getUserById($conn,$id){
        $sql=$conn->prepare("SELECT id, name, email, password_hash, userRole, profilePic FROM users WHERE id = ?");
        $sql->bind_param("i",$id);
        $sql->execute();
        $result=$sql->get_result();
        $user=$result->fetch_assoc();
        $sql->close();
        return $user;
    }
    function updateProfile($conn,$id,$name,$email){
        $sql=$conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $sql->bind_param("ssi",$name,$email,$id);
        $result=$sql->execute();
        $sql->close();
        return $result;
    }
    function updateProfilePic($conn,$id,$filename){
        $sql=$conn->prepare("UPDATE users SET profilePic = ? WHERE id = ?");
        $sql->bind_param("si",$filename,$id);
        $result=$sql->execute();
        $sql->close();
        return $result;
    }
    function updatePassword($conn,$id,$password_hash){
        $sql=$conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $sql->bind_param("si",$password_hash,$id);
        $result=$sql->execute();
        $sql->close();
        return $result;
    }
?>
