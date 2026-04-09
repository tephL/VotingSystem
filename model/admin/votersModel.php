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
            WHERE activated_status = $is_activated AND u.role_id = 1000
            ORDER BY u.user_id DESC
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
            WHERE activated_status = $is_activated AND role_id = 1000
            ORDER BY user_id DESC";
    $r_sql = $conn->query($sql);
    $row = $r_sql->fetch_assoc();

    $total = $row["COUNT"];
    $total_pages = ceil($total / $limit);
    return $page >= $total_pages;
}

// ===========================UPDATE OPERATIONS================================
function acceptUser($user_id){
    $conn = instantiateDbForUser("root", "");
    
    $sql = "CALL AcceptUser('$user_id');";
    $u_sql = $conn->query($sql);
    
    return $u_sql;
}

// ===========================DELETE OPERATIONS================================
function deleteUser($user_id){
    $conn = instantiateDbForUser("root", "");

    $sql = "CALL DeleteUser('$user_id');";;
    $d_sql = $conn->query($sql);

    return $d_sql;
}

?>