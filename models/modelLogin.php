<?php

    function getUserData($conn,$email){
        $sql=$conn->prepare("SELECT id,name,email, password_hash, userRole FROM users Where email=?");
        $sql->bind_param("s",$email);
        $sql->execute();
        $result= $sql->get_result();
        $row=$result->fetch_assoc();
        $sql->close();
        return $row;
    }
