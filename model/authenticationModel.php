<?php

    include(__DIR__ . "/dbconn.php");

    $conn = instantiateDbForUser("root", "");

    
    function userExists($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE BINARY username = '$username'";
        $r_sql = $conn->query($sql);

        if($r_sql->num_rows > 0){
            return true;
        } else{
            return false;
        }
    }
    
    
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


    function passwordValidation($username, $password){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE BINARY username = '$username'";
        $r_sql = $conn->query($sql);

        if($r_sql->num_rows > 0){
            $row = $r_sql->fetch_assoc();
            $passwordFromDB = $row["password"];

            // check if incorrect
            if($password != $passwordFromDB){
                return false;
            }
        } 
        
        return true;
    }


    function getUserActivatedStatus($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE BINARY username = '$username'";
        $r_sql = $conn->query($sql);

        $row = $r_sql->fetch_assoc();
        $activated_status = $row["activated_status"];
        return $activated_status;
    }


    function getUserID($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE BINARY username = '$username'";
        $r_sql = $conn->query($sql);

        $row = $r_sql->fetch_assoc();
        $user_id = $row["user_id"];
        return $user_id;
    }


    function getUserRoleID($username){
        global $conn;
        
        $sql = "SELECT * FROM Users WHERE BINARY username = '$username'";
        $r_sql = $conn->query($sql);

        $row = $r_sql->fetch_assoc();
        $role_id = $row["role_id"];
        return $role_id;
    }

?>