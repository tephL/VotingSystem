<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "3000" && $_SESSION["role"] != "3001" && $_SESSION["role"] != "3002"){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    $username = $_SESSION["username"];
    $role_id = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/mainUI.css">
    <link rel="stylesheet" href="./styles/adminsManager.css">
    <script src="../../src/jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <div id="sidebar">
        <div id="title">
            <h1>UniVote</h1>
            <?php
                if($role_id == 3000){
                    echo "<p id='role_name'>Master Admin</p>";
                } else if($role_id == 3001){
                    echo "<p id='role_name'>Election Admin</p>";
                } else if($role_id == 3002){
                    echo "<p id='role_name'>Voters Admin</p>";
                } 
                echo "<p id='session_username' hidden>$username</p>";
            ?>
        </div>
        <div id="menu_items">
            <a class="items" href="dashboard.php">Dashboard</a>
            <?php
                if($role_id == 3000){
                    echo "<a class='items' href='electionManager.php'>Election Manager</a>";
                    echo "<a class='items' href='adminsManager.php'>Admins Manager</a>";
                } else if($role_id == 3001){
                    echo "<a class='items' href='candidatesManager.php'>Candidates Manager</a>";
                } else if($role_id == 3002){
                    echo "<a class='items' href='votersManager.php'>Voters Manager</a>";
                }
            ?>
            <a class="items" id="signout_btn">Sign Out</a>
        </div>
    </div>


    <div id="view_window_bg">
        <div id="view_window">
            <p id="view_subtext">View User ID</p>
            <div id="view_data">
                <p id="view_status"></p>
                <p id="view_role_name"></p>
                <p id="view_first_name"></p>
                <p id="view_middle_name"></p>
                <p id="view_last_name"></p>
                <p id="view_contact_number"></p>
                <p id="view_email"></p>
            </div>
            <div id="view_actions">
                <button id="view_cancel" onclick="cancelView()">Cancel</button>
            </div>
        </div>
    </div>


    <div id="new_window_bg">
        <div id="new_window">
            <p id="new_subtext">New User ID</p>
            <div id="new_hint">
                <p id="new_hint_text" hidden></p>
            </div>
            <input type="text" id="new_first_name" placeholder="First Name">
            <input type="text" id="new_middle_name" placeholder="Middle Name">
            <input type="text" id="new_last_name" placeholder="Last Name">
            <input type="text" id="new_contact_number" placeholder="Contact Number">
            <input type="text" id="new_user_id" hidden>
            <input type="text" id="new_username" placeholder="New Username">
            <input type="email" id="new_email" placeholder="New Email">
            <input type="text" id="new_password" placeholder="New Password">
            <select id="new_status">
                <option id="new_activated" value="1">Activated</option>
                <option id="new_deactivated" value="0" selected>Deactivated</option>
            </select>
            <select id="new_role">
                <option id="new_master_admin" value="3000">Master Admin</option>
                <option id="new_election_admin" value="3001">Election Admin</option>
                <option id="new_voters_admin" value="3002">Voters Admin</option>
            </select>
            <div id="new_actions">
                <button id="new_cancel" onclick="cancelCreation()">Cancel</button>
                <button id="new_submit" onclick="submitNewAdminInfo()">Submit</button>
            </div>
        </div>
    </div>

    <div id="edit_window_bg">
        <div id="edit_window">
            <p id="edit_subtext">Edit User ID</p>
            <div id="edit_hint">
                <p id="edit_hint_text" hidden></p>
            </div>
            <input type="text" id="edit_first_name" placeholder="First Name">
            <input type="text" id="edit_middle_name" placeholder="Middle Name">
            <input type="text" id="edit_last_name" placeholder="Last Name">
            <input type="text" id="edit_contact_number" placeholder="Contact Number">
            <input type="text" id="edit_user_id" hidden>
            <input type="text" id="old_username" hidden>
            <input type="text" id="old_role_id" hidden>
            <input type="text" id="edit_username" placeholder="New Username">
            <input type="email" id="edit_email" placeholder="New Email">
            <input type="text" id="edit_password" placeholder="New Password">
            <select id="edit_status" placeholder="Status">
                <option id="edit_activated" value="1">Activated</option>
                <option id="edit_deactivated" value="0">Deactivated</option>
            </select>
            <select id="edit_role" placeholder="Role">
                <option id="edit_master_admin" value="3000">Master Admin</option>
                <option id="edit_election_admin" value="3001">Election Admin</option>
                <option id="edit_voters_admin" value="3002">Voters Admin</option>
            </select>
            <div id="edit_actions">
                <button id="edit_cancel" onclick="cancelEdit()">Cancel</button>
                <button id="edit_submit" onclick="submitUpdatedAdminInfo()">Submit</button>
            </div>
        </div>
    </div>
    
    <div id="main_content">
        <div id="admins"></div>
    </div>

    <script src="./scripts/adminUtils.js"></script>
    <script src="./scripts/adminsManager.js"></script>
</body>
</html>
