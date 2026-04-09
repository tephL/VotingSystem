<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "3000"){ 
        header("Location: ../unauthorized.html"); 
        exit(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/Results.css">
    <script src="../../src/jquery.js"></script>
    <script src="../../src/chart.js"></script>
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
 
        <h2 id="greeting">Good Day,</h2>
 
        <!-- No Election -->
        <div id="no_election_section">
            <h2>No Election have been started yet</h2>
            <p>Create your first election to get Started</p>
            <button onclick="window.location.href='./electionManager.php';">Election Manager</button>
        </div>
 
        <!-- Ongoing Election: Dynamically Filled -->
        <div id="ongoing_election_section">
            <div id="ongoing_election_title"></div>
          
            <div id="ongoing_charts_container"></div>
        </div>
 
        <!-- Closed Election: Dynamicaly Filled -->
        <div id="closed_election_section">
            <div id="closed_election_title">Results</div>
 
            <!-- Winner Cards -->
            <div id="winners_container"></div>
 
            <!-- Charts -->
            <div id="closed_charts_container"></div>
        </div>
 
    </div>
    <script src="./scripts/adminUtils.js"></script>
</body>
</html>
