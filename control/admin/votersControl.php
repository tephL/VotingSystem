<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . "/../../model/admin/readOperations.php");

$action = $_POST['action'];
switch($action){
    case "getDeactivatedUsers":
        returnDeactivatedUsers();
        break;
    default:
        echo json_encode([
            "message" => "action doesnt exist"
        ]);
        break;
}

function returnDeactivatedUsers(){
    
    $deactivated_users = getDeactivatedUsers();
    if($deactivated_users->num_rows < 1) echo json_encode([
        "message" => "no deactivated users"
    ]);

    $deactivateds = [];
    $rows = $deactivated_users->fetch_all(MYSQLI_ASSOC);

    foreach($rows as $row){
        $user_id = $row["user_id"];
        $username = $row["username"];
        $email = $row["email"];
        $student_id = $row["student_id"];

        $account = array(
            "user_id" => $user_id,
            "username" => $username,
            "email" => $email,
            "student_id" => $student_id
        );

        array_push($deactivateds, $account);
    }

    echo json_encode([
        "message" => "deactivated users",
        "deactivated_users" => $deactivateds
    ]);

}

function renderDeactivatedUsers(){
    
    if($deactivated_users-> num_rows > 0){

        echo '<h2>Users that hasnt been activated</h2>';

        echo '<table id="deactivated_users_table">
                <tr>
                    <th>User ID</th>
                    <th>Student ID</th>
                    <th>Username</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>';

        while($row = $deactivated_users->fetch_assoc()){
            echo '<tr>
                    <td>'.$row["user_id"].'</td>
                    <td>2024100749</td>
                    <td>'.$row["username"].'</td>
                    <td>'.$row["created_date"].'</td>';
            echo '<td><div class="action_box">';
            echo '<button class="action">Activate</button>';
            echo '<button class="action">Reject</button>';
            echo '</div></td>';
            echo '</tr>';
        }
    } else{
        echo "users are activated";
    }
    return;
}

?>