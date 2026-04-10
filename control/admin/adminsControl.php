<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . "/../../model/admin/adminsModel.php");
include_once(__DIR__ . "/../../model/admin/readOperations.php");

$action = $_POST["action"];
switch($action){
    case "getAdmins":
        returnAdmins();
        break;
    case "updateAdminInfo":
        validateEditAdminInfo();
        break;
    case "deleteAdminWithUserId":
        deleteAdminWithUserId();
        break;
    case "createNewAdmin":
        validateNewAdminInfo();
        break;
    default:
        echo json_encode([
            "message" => "action doesnt exist"
        ]);
        break;
}


function validateEditAdminInfo(){
    $new_data = $_POST["new_data"];

    $user_id = $new_data["user_id"];
    $first_name = $new_data["new_first_name"];
    $middle_name = $new_data["new_middle_name"];
    $last_name = $new_data["new_last_name"];
    $contact_number = $new_data["new_contact_number"];
    $activated_status = $new_data["new_activated_status"];

    $email = $new_data["new_email"];
    $old_username = $new_data["old_username"];
    $username = $new_data["new_username"];
    $password = $new_data["new_password"];
    $role_id = $new_data["new_role_id"];
    
    // data validation
    // name validations
    // first name / last name empty checker
    if(strlen($first_name) <= 1 || strlen($last_name) <= 1){
        echo json_encode([
            "status" => false,
            "message" => "first name and last name required" 
        ]);
        return;
    };

    // contact number validation
    if(strlen($contact_number) < 9 || strlen($contact_number) > 12){
        echo json_encode([
            "status" => false,
            "message" => "invalid contact number" 
        ]);
        return;
    };


    // username validaiton
    // no whitespaces
    if(str_contains($username, ' ') || str_contains($password, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "username or password cant contain whitespaces" 
        ]);
        return;
    }
    // is it less than minimum char of the usern (5)
    if(strlen($username) <= 4){
        echo json_encode([
            "status" => false,
            "message" => "username must be more than 4 characters" 
        ]);
        return;
    };
    $is_username_same = isUsernameSame($old_username, $username);
    if(!$is_username_same){
        if(userExists($username)){
            echo json_encode([
                "status" => false,
                "message" => "username already taken" 
            ]);
            return;
        }
    }

    // email validation
    if(str_contains($email, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "email cant contain whitespaces" 
        ]);
        return;
    }

    if(strlen($email) <= 5){
        echo json_encode([
            "status" => false,
            "message" => "email must not be valid" 
        ]);
        return;
    };
    
    // password validation
    // is it less than minimum char of the passw (8)
    if(strlen($password) <= 7){
        echo json_encode([
            "status" => false,
            "message" => "password must be more or equal than 8 characters" 
        ]);
        return;
    };

    updateAdminInfo($user_id, $role_id, $username, $email, $password, $activated_status, $first_name, $middle_name, $last_name, $contact_number);

    echo json_encode([
        "message" => "hi",
        "status" => true,
        "data" => $new_data
    ]);
    return;
}


function validateNewAdminInfo(){
    $new_data = $_POST["new_data"];

    $first_name = $new_data["new_first_name"];
    $middle_name = $new_data["new_middle_name"];
    $last_name = $new_data["new_last_name"];
    $contact_number = $new_data["new_contact_number"];

    $email = $new_data["new_email"];
    $username = $new_data["new_username"];
    $password = $new_data["new_password"];
    $role_id = $new_data["new_role_id"];
    
    // data validation
    // name validations
    // first name / last name empty checker
    if(strlen($first_name) <= 1 || strlen($last_name) <= 1){
        echo json_encode([
            "status" => false,
            "message" => "first name and last name required" 
        ]);
        return;
    };

    // contact number validation
    if(strlen($contact_number) < 9 || strlen($contact_number) > 12){
        echo json_encode([
            "status" => false,
            "message" => "invalid contact number" 
        ]);
        return;
    };


    // username validaiton
    // no whitespaces
    if(str_contains($username, ' ') || str_contains($password, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "username or password cant contain whitespaces" 
        ]);
        return;
    }
    // is it less than minimum char of the usern (5)
    if(strlen($username) <= 4){
        echo json_encode([
            "status" => false,
            "message" => "username must be more than 4 characters" 
        ]);
        return;
    };
    $userExistence = userExists($username);
    if($userExistence){
        echo json_encode([
            "status" => false,
            "message" => "username already taken" 
        ]);
        return;
    }

    // email validation
    if(str_contains($email, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "email cant contain whitespaces" 
        ]);
        return;
    }

    if(strlen($email) <= 5){
        echo json_encode([
            "status" => false,
            "message" => "email must not be valid" 
        ]);
        return;
    };
    
    // password validation
    // is it less than minimum char of the passw (8)
    if(strlen($password) <= 7){
        echo json_encode([
            "status" => false,
            "message" => "password must be more or equal than 8 characters" 
        ]);
        return;
    };

    registerAdmin($first_name, $middle_name, $last_name, $contact_number, $email, $username, $password, $role_id);

    echo json_encode([
        "message" => "hi",
        "status" => true,
        "data" => $new_data
    ]);
    return;
}


function deleteAdminWithUserId(){
    $user_id = $_POST["user_id"];

    deleteAdmin($user_id);

    echo json_encode([
        "user" => $user_id
    ]);
}


function returnAdmins(){

    $page = $_POST["page"];
    
    $limit = 10;
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
        $first_name = $row["first_name"];
        $middle_name = $row["middle_name"];
        $last_name = $row["last_name"];
        $contact_number = $row["contact_number"];

        $account = array(
            "activated_status" => $activated_status,
            "user_id" => $user_id,
            "role_name" => $role_name,
            "role_id" => $role_id,
            "username" => $username,
            "email" => $email,
            "created_date" => $created_date,
            "first_name" => $first_name,
            "middle_name" => $middle_name,
            "last_name" => $last_name,
            "contact_number" => $contact_number
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


function isUsernameSame($old_username, $new_username){
    if($old_username == $new_username){
        return true;
    } else{
        return false;
    }
}

?>