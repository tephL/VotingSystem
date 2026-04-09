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

    $role_id = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/adminUI.css">
    <link rel="stylesheet" href="./styles/adminsManager.css">
    <script src="../../src/jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <div id="sidebar">
        <div id="title">
            <h1>Voting System</h1>
            <?php
                if($role_id == 3000){
                    echo "<p id='role_name'>Master Admin</p>";
                } else if($role_id == 3001){
                    echo "<p id='role_name'>Election Admin</p>";
                } else if($role_id == 3002){
                    echo "<p id='role_name'>Voters Admin</p>";
                } 
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

    <div id="edit_window_bg">
        <div id="edit_window">
            <p id="edit_subtext">Edit User ID</p>
            <div id="edit_hint">
                <p id="edit_hint_text" hidden></p>
            </div>
            <input type="text" id="edit_user_id" hidden>
            <input type="text" id="old_username" hidden>
            <input type="text" id="edit_username" placeholder="New Username">
            <input type="email" id="edit_email" placeholder="New Email">
            <input type="text" id="edit_password" placeholder="New Password">
            <select id="edit_status">
                <option selected>Activate</option>
                <option>Deactivate</option>
            </select>
            <select id="edit_role">
                <option id="edit_master_admin">Master Admin</option>
                <option id="edit_election_admin">Election Admin</option>
                <option id="edit_voters_admin">Voters Admin</option>
            </select>
            <div id="edit_actions">
                <button id="edit_cancel" onclick="cancelEdit()">Cancel</button>
                <button id="edit_submit" onclick="submitUpdatedUserInfo()">Submit</button>
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
