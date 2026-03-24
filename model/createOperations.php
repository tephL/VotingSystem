<?php 

    function registerStudent($conn,$studentid,$username,$email,$password){
        $sql = "INSERT INTO Users (student_id, username, email, password, role_id) 
        VALUES ('$studentid', '$username', '$email', '$password', 1001)";
        $result = $conn->query($sql);
        return $result;
    }
?>