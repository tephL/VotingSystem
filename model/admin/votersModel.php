<?php
include_once(__DIR__ . "/../dbconn.php");


// ===========================READ OPERATIONS================================
function getTypeOfUsers($is_activated, $limit, $offset){
    $activated_status;
    if($is_activated){ $activated_status = 1; }
    else{ $activated_status = 0; }

    $conn = instantiateDbForUser("root", "");

    $sql = "SELECT
                u.user_id,
                u.username,
                u.email,
                u.created_date,
                sv.student_id
            FROM Users u
            LEFT JOIN StudentVoters sv
                ON sv.user_id = u.user_id
            WHERE u.activated_status = $activated_status AND u.role_id = 1000
            ORDER BY u.user_id ASC
            LIMIT $limit
            OFFSET $offset;";
    $r_sql = $conn->query($sql);

    return $r_sql;
}


function isTypeOfUsersLastPage($is_activated, $page, $limit){
    $activated_status;
    if($is_activated){ $activated_status = 1; }
    else{ $activated_status = 0; }
    
    $conn = instantiateDbForUser("root", "");

    $sql = "SELECT COUNT(*) AS COUNT 
            FROM Users 
            WHERE activated_status = $activated_status AND role_id = 1000
            ORDER BY user_id ASC";
    $r_sql = $conn->query($sql);
    $row = $r_sql->fetch_assoc();


    $total = $row["COUNT"];
    $total_pages = ceil($total / $limit);
    return $page >= $total_pages;
}


function userExists($username){
    $conn = instantiateDbForUser("root", "");
    
    $sql = "SELECT * FROM Users WHERE BINARY username = '$username'";
    $r_sql = $conn->query($sql);

    if($r_sql->num_rows > 0){
        return true;
    } else{
        return false;
    }
}


function getPasswordWithUserId($user_id){
    $conn = instantiateDbForUser("root", "");

    $sql = "SELECT password FROM Users WHERE user_id = $user_id";
    $r_sql = $conn->query($sql);

    $row = $r_sql->fetch_assoc();
    $password = $row["password"];
    return $password;
}


function isUserInStudentsDb($student_id){
    $conn = instantiateDbForUser("root", "");
    
    $sql = "SELECT * FROM Students WHERE student_id = '$student_id'";
    $r_sql = $conn->query($sql);

    if($r_sql->num_rows > 0){
        return true;
    }
    return false;
}

// ===========================UPDATE OPERATIONS================================
function acceptUser($user_id){
    $conn = instantiateDbForUser("root", "");
    
    $sql = "CALL AcceptUser('$user_id');";
    $u_sql = $conn->query($sql);
    
    return $u_sql;
}


function editUserDetails($user_id, $student_id, $username, $email, $password, $activated_status){
    $conn = instantiateDbForUser("root", "");

    $sql = "CALL UpdateStudentVoterInfo('$user_id', '$username', '$email', '$password', '$activated_status', '$student_id');";
    $conn->query($sql);
}

// ===========================DELETE OPERATIONS================================
function deleteUser($user_id){
    $conn = instantiateDbForUser("root", "");

    $sql = "CALL DeleteUser('$user_id');";;
    $d_sql = $conn->query($sql);

    return $d_sql;
}

?>