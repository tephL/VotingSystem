<?php
include_once(__DIR__ . "/../dbconn.php");


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
                u.created_date
            FROM Users u 
            LEFT JOIN Admins a
                ON a.user_id = u.user_id
            LEFT JOIN Roles r 
                ON r.role_id = u.role_id
            WHERE u.role_id >= 3000
            ORDER BY u.user_id ASC
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
            ORDER BY user_id ASC";
    $r_sql = $conn->query($sql);
    $row = $r_sql->fetch_assoc();


    $total = $row["COUNT"];
    $total_pages = ceil($total / $limit);
    return $page >= $total_pages;
}
?>