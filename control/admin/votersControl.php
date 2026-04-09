<?php

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
        rejectUserWithUserId();
        break;
    case "acceptUserWithUserId":
        acceptUserWithUserId();
        break
    case "deleteUserWithUserId":
        deleteUserWithUserId();
        break;
    default:
        echo json_encode([
            "message" => "action doesnt exist"
        ]);
        break;
}


function deleteUserWithUserId(){
    $user_id = $_POST["user_id"];
    deleteUser($user_id);
    
    echo json_encode([
        "message" => "success"
    ]);
    return;
}


function rejectUserWithUserId(){
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
        $is_activated = 1;
    } else if($type == "deactivated"){
        $is_activated = 0; 
    }

    $page = $_POST["page"];
    
    $limit = 8;
    $offset = ($page - 1) * $limit;
    
    $type_of_users = getTypeOfUsers($is_activated, $limit, $offset);
    $is_last_page = isTypeOfUsersLastPage($is_activated, $page, $limit);

    if($type_of_users->num_rows < 1){
        echo json_encode([
            "status" => false,
            "limit" => $limit
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
        "deactivated_users" => $users,
        "limit" => $limit,
        "is_last_page" => $is_last_page
    ]);
    
    return;

}

?>