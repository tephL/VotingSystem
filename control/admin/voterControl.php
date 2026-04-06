<?php

include(__DIR__ . "/../../model/admin/readOperations.php");

function renderDeactivatedUsers(){
    $deactivated_users = getDeactivatedUsers();
    
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