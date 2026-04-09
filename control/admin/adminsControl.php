<?php

include(__DIR__ . "/../../model/admin/adminsModel.php");

$action = $_POST["action"];
switch($action){
    case "getAdmins":
        returnAdmins();
        break;
    default:
        echo json_encode([
            "message" => "action doesnt exist"
        ]);
        break;
}

function returnAdmins(){

    $page = 1;//$_POST["page"];
    
    $limit = 8;
    $offset = ($page - 1) * $limit;
    
    $admin_users = getAdminUsers($limit, $offset);
    $is_last_page = isAdminsLastPage($page, $limit);

    if($admin_users->num_rows < 1){
        echo json_encode([
            "status" => false,
            "is_last_page" => $is_last_page
        ]); 
        return;
    }

    $admins = [];
    $rows = $admin_users->fetch_all(MYSQLI_ASSOC);

    foreach($rows as $row){
        $activated_status = $row["activated_status"];
        $user_id = $row["user_id"];
        $role_name = $row["role_name"];
        $role_id = $row["role_id"];
        $username = $row["username"];
        $email = $row["email"];
        $created_date = $row["created_date"];

        $account = array(
            "activated_status" => $activated_status,
            "user_id" => $user_id,
            "role_name" => $role_name,
            "role_id" => $role_id,
            "username" => $username,
            "email" => $email,
            "created_date" => $created_date
        );

        array_push($admins, $account);
    }

    echo json_encode([
        "status" => true,
        "admins" => $admins,
        "is_last_page" => $is_last_page
    ]);
    
    return;
}

?>