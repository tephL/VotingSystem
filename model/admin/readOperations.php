<?php

    include (__DIR__ . "/../dbconn.php");

    $conn = instantiateDbForUser("root", "");

    function userExists($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $r_sql = $conn->query($sql);

        if($r_sql->num_rows > 0){
            return true;
        } else{
            return false;
        }
    }
    // for registration form
    function isEnrolledStudent($studentid){
        global $conn;
        
        $sql = "SELECT * FROM Students WHERE student_id = '$studentid'";
        $r_sql = $conn->query($sql);
         return $r_sql->num_rows > 0;
    }
    function studentIDExists($studentid){
        global $conn;
        
        $sql = "SELECT * FROM StudentVoters WHERE student_id = '$studentid'";
        $r_sql = $conn->query($sql);
        return $r_sql -> num_rows > 0;
    }
    function emailExists($email){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE email = '$email'";
        $r_sql = $conn->query($sql);
        return $r_sql -> num_rows > 0;
    }

    function passwordValidation($username, $password){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $r_sql = $conn->query($sql);

        if($r_sql->num_rows > 0){
            $row = $r_sql->fetch_assoc();
            $passwordFromDB = $row["password"];

            if($password != $passwordFromDB){
                return false;
            }
        } 
        
        return true;
    }
    function getUserID($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $r_sql = $conn->query($sql);

        $row = $r_sql->fetch_assoc();
        $user_id = $row["user_id"];
        return $user_id;
    }

    function getUserRoleID($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $r_sql = $conn->query($sql);

        $row = $r_sql->fetch_assoc();
        $role_id = $row["role_id"];
        return $role_id;
    }

    function getUserActivatedStatus($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $r_sql = $conn->query($sql);

        $row = $r_sql->fetch_assoc();
        $activated_status = $row["activated_status"];
        return $activated_status;
    }

    function getDeactivatedUsers(){
        global $conn;

        $sql = "SELECT 
                user_id, 
                username, 
                created_date 
                FROM Users 
                WHERE activated_status = 0;";
        $r_sql = $conn->query($sql);

        return $r_sql;
    }

?>
