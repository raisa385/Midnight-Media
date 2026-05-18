<?php
 function checkEmail($conn,$email){
    $sql = $conn->prepare("SELECT id from users where email=?")
    $sql->bind_param("s",$email);
    $sql->execute();
    $result=$sql->get_result();
    $rows=$result->num_rows();
    $sql->close();
    return $rows
 }
 function registerUser($conn,$name,$email,$password,$userRole){
    $sql=$conn->prepare("INSERT INTO users(name, email,password_hash,userRole) VALUES (?,?,?,?)");
    $sql->bind_param("ssss",$name,$email,$password_hash,$userRole);
    $result=$sql->execute();
    $sql->close();
    return $result;
 }