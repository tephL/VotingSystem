<?php
include_once(__DIR__ . "/../dbconn.php");

// ===========================CREATE OPERATIONS================================
function registerAdmin($first_name, $middle_name, $last_name, $contact_number, $email, $username, $password, $role_id){
    $conn = instantiateDbForUser("root", "");

    $sql = "CALL RegisterAdmin('$first_name', '$middle_name', '$last_name', '$contact_number', '$email', '$username', '$password', '$role_id')";
    $c_sql = $conn->query($sql);
}


// ===========================READ OPERATIONS================================
function getAdminUsers($limit, $offset){

    $conn = instantiateDbForUser("root", "");

    $sql = "SELECT
                u.activated_status,
                u.user_id,
                u.role_id,
                r.role_name,
                u.username,
                u.email,
                u.created_date,
                a.first_name,
                a.middle_name,
                a.last_name,
                a.contact_number
            FROM Users u 
            LEFT JOIN Admins a
                ON a.user_id = u.user_id
            LEFT JOIN Roles r 
                ON r.role_id = u.role_id
            WHERE u.role_id >= 3000
            ORDER BY u.activated_status DESC
            LIMIT $limit
            OFFSET $offset;";
    $r_sql = $conn->query($sql);

    return $r_sql;
}


function isAdminsLastPage($page, $limit){
    
    $conn = instantiateDbForUser("root", "");

    $sql = "SELECT COUNT(*) AS COUNT 
            FROM Users 
            WHERE role_id >= 3000
            ORDER BY activated_status DESC";
    $r_sql = $conn->query($sql);
    $row = $r_sql->fetch_assoc();


    $total = $row["COUNT"];
    $total_pages = ceil($total / $limit);
    return $page >= $total_pages;
}


// ===========================UPDATE OPERATIONS================================
function updateAdminInfo($user_id, $role_id, $username, $email, $password, $activated_status, $first_name, $middle_name, $last_name, $contact_number){

    $conn = instantiateDbForUser("root", "");

    $sql = "CALL UpdateAdminInfo('$user_id', '$role_id', '$username', '$email', '$password', '$activated_status', '$first_name', '$middle_name', '$last_name', '$contact_number')";
    $u_sql = $conn->query($sql);

    return;
}


// ===========================DELETE OPERATIONS================================
function deleteAdmin($user_id){

    $conn = instantiateDbForUser("root", "");

    $sql = "CALL DeleteAdmin('$user_id');";
    $conn->query($sql);
}

?>