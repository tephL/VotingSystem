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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/results.css">
    <script src="../chart.js"></script>
    <script src="../jquery.js"></script>
</head>
<body>
        <div id="sidebar">
        <div id="title">
            <h1>Voting System</h1>
            <p>Admin Page</p>
        </div>
        <div id="menu_items">
            <a class="items" href="dashboard.php">Dashboard</a>
            <a class="items" href="./results.php">Results</a>
            <a class="items" href="./electionManager.php">Election Manager</a>
            <a class="items" href="./candidatesManager.php">Candidates Manager</a>
            <a class="items" href="./votersManager.php">Voters Manager</a>
            <a class="items" href="./adminSettings.php">Settings</a>
        </div>
    </div>

    <div id="main_content">

    <!--No Election-->
    <h2 id="greeting"> Good Day,</h2>
    <div id="no_election_section"> 
    <h2>No Election have been started yet</h2>
    <p>Create your first election to get Started</p>
    <button onclick="window.location.href='./electionManager.php';">Election Manager</button>
    </div>

    <!--Ongoing Election -->
    <div id="ongoing_election_section"> 
            <div id="ongoing_election_title">Student Council</div>
            <h2>President Ranking</h2>
            <canvas id="chart_president"></canvas>
            
            <h2>Vice-President Ranking</h2>
            <canvas id="chart_vicepresident"></canvas>

            <h2>Senator Ranking</h2>
            <canvas id="chart_senator"></canvas>

            <h2>Vice-Governor Ranking</h2>
            <canvas id="chart_vicegovernor"></canvas>
    </div>
    
<!--Closed Election-->

    <div id="closed_election_section">
        <div id="closed_election_title">Result</div>
    
        <!-- Winner Cards -->
    <div id="winners">
        <div class="winner_card" id="winner_president"></div>
        <div class="winner_card" id="winner_vicepresident"></div>
        <div class="winner_card" id="winner_senator"></div>
        <div class="winner_card" id="winner_vicegovernor"></div>
    </div>
    
    <!-- President -->
    <h2>President Vote Share</h2>
    <canvas id="pie_chart_president"></canvas>
    <h2>President Vote Ranking</h2>
    <canvas id="bar_chart_president"></canvas>

    <!-- Vice-President -->
    <h2>Vice-President Vote Share</h2>
    <canvas id="pie_chart_vicepresident"></canvas>
    <h2>Vice-President Vote Ranking</h2>
    <canvas id="bar_chart_vicepresident"></canvas>

    <!-- Senator -->
    <h2>Senator Vote Share</h2>
    <canvas id="pie_chart_senator"></canvas>
    <h2>Senator Vote Ranking</h2>
    <canvas id="bar_chart_senator"></canvas>

    <!-- Vice-Governor -->
    <h2>Vice-Governor Vote Share</h2>
    <canvas id="pie_chart_vicegovernor"></canvas>
    <h2>Vice-Governor Vote Ranking</h2>
    <canvas id="bar_chart_vicegovernor"></canvas>

</div>
    
    </div>
    <script src="adminUtils.js"></script>
</body>
</html>