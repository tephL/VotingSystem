<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "3000"){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    $role_id = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/adminUI.css">
    <script src="../../src/jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Manager</title>
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
    
    <div id="main_content">
        <div class="options">
            <p id="election_status">Theres no ongoing election</p>
            <button id="start_election">start election</button>
        </div>
    </div>

    <script src="./scripts/adminUtils.js"></script>
</body>
</html>
