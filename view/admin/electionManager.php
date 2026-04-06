<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "1000"){ 
        header("Location: ../unauthorized.html"); 
        exit(); 
    }
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
            <p>Admin Page</p>
        </div>
        <div id="menu_items">
            <a class="items" href="dashboard.php">Dashboard</a>
            <a class="items" href="./electionManager.php">Election Manager</a>
            <a class="items" href="./candidatesManager.php">Candidates Manager</a>
            <a class="items" href="./votersManager.php">Voters Manager</a>
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
