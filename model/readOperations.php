<?php

    include(__DIR__ . "/dbconn.php");

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

            // check if incorrect
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



// =================== Election Results Read Operations ===========================================================================

    function getCurrentElection() {
        global $conn;
        $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections ORDER BY election_id DESC LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }

    function getPositions($election_id) {
        global $conn;
        $eid = intval($election_id);
        $sql = "SELECT position_id, position_name FROM Positions WHERE election_id = $eid ORDER BY position_id ASC";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            error_log("Query error: " . mysqli_error($conn));
            return null;
        }
        
        $positions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $positions[] = $row;
        }
        
        return $positions;
    }

    function getCandidates($position_id, $election_id) {
        global $conn;
        $pid = intval($position_id);
        $eid = intval($election_id);
        
        $sql = "
            SELECT 
                c.candidate_id,
                CONCAT(s.first_name, ' ', IFNULL(CONCAT(s.middle_name, ' '), ''), s.last_name) AS candidate_name,
                COUNT(v.vote_id) AS vote_count
            FROM Candidates c
            JOIN Students s ON c.student_id = s.student_id
            LEFT JOIN Votes v ON c.candidate_id = v.candidate_id AND v.position_id = c.position_id AND v.election_id = $eid
            WHERE c.position_id = $pid
            GROUP BY c.candidate_id
            ORDER BY vote_count DESC, c.candidate_id ASC
        ";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            error_log("Query error: " . mysqli_error($conn));
            return null;
        }
        
        $candidates = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $candidates[] = $row;
        }
        
        return $candidates;
    }
?>