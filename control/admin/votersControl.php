<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include(__DIR__ . "/../../model/admin/votersModel.php");


$action = $_POST['action'];
switch($action){
    case "getDeactivatedUsers":
        returnTypeOfUsers("deactivated");
        break;
    case "getActivatedUsers":
        returnTypeOfUsers("activated");
        break;
    case "rejectUserWithUserId":
        deleteUserWithUserId();
        break;
    case "acceptUserWithUserId":
        acceptUserWithUserId();
        break;
    case "deleteUserWithUserId":
        deleteUserWithUserId();
        break;
    case "getUserPassword":
        returnUserPassword();
        break;
    case "updateUserInfo":
        updateUserInfo();
        break;
    default:
        echo json_encode([
            "message" => "action doesnt exist"
        ]);
        break;
}


function updateUserInfo(){
    $new_data = $_POST["new_data"];

    $user_id = $new_data["user_id"];
    $new_username = $new_data["new_username"];
    $old_username = $new_data["old_username"];
    $new_email = $new_data["new_email"];
    $new_password = $new_data["new_password"];
    $new_student_id = $new_data["new_student_id"];
    $new_activated_status = $new_data["new_activated_status"];

    // data validation
    // no whitespaces
    if(str_contains($new_username, ' ') || str_contains($new_password, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "username or password cant contain whitespaces" 
        ]);
        return;
    }

    if(str_contains($new_email, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "email cant contain whitespaces" 
        ]);
        return;
    }

    if(str_contains($new_student_id, ' ')){
        echo json_encode([
            "status" => false,
            "message" => "student id cant contain whitespaces" 
        ]);
        return;
    }

    // is it less than minimum char of the usern (5)
    if(strlen($new_username) <= 4){
        echo json_encode([
            "status" => false,
            "message" => "username must be more than 4 characters" 
        ]);
        return;
    };

    // is it less than minimum char of the passw (8)
    if(strlen($new_password) <= 7){
        echo json_encode([
            "status" => false,
            "message" => "password must be more or equal than 8 characters" 
        ]);
        return;
    };

    // is student id 10
    if (strlen($new_student_id) !== 10) {
        echo json_encode([
            "status" => false,
            "message" => "student id must be exactly 10 characters" 
        ]);
        return;
    }

    // same username scenario
    $isUsernameSame = isUsernameSame($old_username, $new_username);
    if(!$isUsernameSame){
        $userExistence = userExists($new_username);
        if($userExistence){
            echo json_encode([
                "status" => false,
                "message" => "username already taken" 
            ]);
            return;
        }
    }

    editUserDetails($user_id, $new_student_id, $new_username, $new_email, $new_password, $new_activated_status);

    echo json_encode([
        "received" => $new_data,
        "status" => true,
        "x" => $new_username
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


function returnUserPassword(){
    $user_id = $_POST["user_id"];

    $password = getPasswordWithUserId($user_id);
    echo json_encode([
        "user_id" => $user_id,
        "password" => $password
    ]);
    return;
}


function deleteUserWithUserId(){
    $user_id = $_POST["user_id"];
    deleteUser($user_id);
    
    echo json_encode([
        "message" => "success"
    ]);
    return;
}


function acceptUserWithUserId(){
    $user_id = $_POST["user_id"];
    acceptUser($user_id);

    echo json_encode([
        "message" => "success"
    ]);
    return;
}


function returnTypeOfUsers($type){

    $is_activated;
    if($type == "activated"){ 
        $is_activated = true;
    } else if($type == "deactivated"){
        $is_activated = false; 
    }

    $page = $_POST["page"];
    
    $limit = 8;
    $offset = ($page - 1) * $limit;
    
    $type_of_users = getTypeOfUsers($is_activated, $limit, $offset);
    $is_last_page = isTypeOfUsersLastPage($is_activated, $page, $limit);

    if($type_of_users->num_rows < 1){
        echo json_encode([
            "status" => false,
            "limit" => $limit,
            "is_last_page" => $is_last_page
        ]); 
        return;
    }

    $users = [];
    $rows = $type_of_users->fetch_all(MYSQLI_ASSOC);

    foreach($rows as $row){
        $user_id = $row["user_id"];
        $username = $row["username"];
        $email = $row["email"];
        $student_id = $row["student_id"];
        $created_date = $row["created_date"];

        $account = array(
            "user_id" => $user_id,
            "username" => $username,
            "email" => $email,
            "student_id" => $student_id,
            "created_date" => $created_date
        );

        array_push($users, $account);
    }

    echo json_encode([
        "status" => true,
        "users" => $users,
        "limit" => $limit,
        "is_last_page" => $is_last_page
    ]);
    
    return;

}

?>