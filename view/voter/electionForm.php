<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "1001"){ 
        header("Location: ../../index.html"); 
        exit(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/voterUI.css">
    <link rel="stylesheet" href="./styles/electionForm.css">
    <script src="../../src/jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Form</title>
</head>
<body>

    <div id="sidebar">
        <div id="title">
            <h1>Voting System</h1>
            <p>Voter Page</p>
        </div>
        <div id="menu_items">
            <a class="items" href="dashboard.php">Dashboard</a>
            <a class="items" href="./electionForm.php">Election Form</a>
            <a class="items" id="signout_btn">Sign Out</a>
        </div>
    </div>
    
    <div id="main_content">
        <h1 id="election_title"></h1>
        <p id="election_year"></p>
    </div>


    <script src="./scripts/voterUtils.js"></script>
    <script src="./scripts/electionForm.js"></script>
</body>
</html>
