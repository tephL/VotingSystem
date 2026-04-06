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
    <script src="../jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            <a class="items" href="./adminSettings.php">Settings</a>
        </div>
    </div>
    
    <div id="main_content">
        <div class="graph" id="live_report_president">Live standing of President</div>
        <div class="graph" id="live_report_vicepresident">Live standing of Vice President</div>
        <div class="graph" id="live_report_mayors">Live standing of Mayors</div>
        <div class="graph" id="live_report_boardmembers">Live standing of Board Members</div>
        <div class="graph" id="live_report_brainrots">Live standing of Brainrots</div>
        <div class="graph" id="live_report_animals">Live standing of Animals</div>
    </div>

    <script src="./adminUtils.js"></script>
</body>
</html>
