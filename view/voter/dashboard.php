<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "1000"){ 
        header("Location: ../../index.html"); 
        exit(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/mainUI.css">
    <link rel="stylesheet" href="../admin/styles/Results.css">
    <script src="../../src/jquery.js"></script>
    <script src="../../src/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <div id="sidebar">
        <div id="title">
            <h1>UniVote</h1>
            <p id="role_name">Student Voter</p>
        </div>
        <div id="menu_items">
            <a class="items" href="dashboard.php">Dashboard</a>
            <a class="items" href="./electionForm.php">Election Form</a>
            <a class="items" href="./voteHistory.php">Vote History</a>
            <a class="items" id="signout_btn">Sign Out</a>
        </div>
    </div>
    
    <div id="main_content">
 
        <h2 id="greeting">Good Day,</h2>

        <div id="no_election_section">
            <h2>No Election have been started yet</h2>
            <p>Check back later for upcoming elections</p>
        </div>

        <!-- Upcoming Election: Candidates Only -->
        <div id="upcoming_election_section">
            <div id="upcoming_election_title"></div>
          
            <div id="upcoming_charts_container"></div>
        </div>

        <!-- Ongoing Election: Wait Message -->
        <div id="ongoing_election_section">
            <div id="ongoing_election_content">
                <h2 id="ongoing_election_title"></h2>
                <div class="ongoing_message">
                    <p>The election is currently ongoing.</p>
                    <p>Results will be displayed after the election is completed.</p>
                    <p>Please check back later.</p>
                </div>
            </div>
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

    <script src="./scripts/voterUtils.js"></script>
    <script src="./scripts/voterDashboard.js"></script>
</body>
</html>
