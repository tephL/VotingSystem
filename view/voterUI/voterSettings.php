<?php 
    // validation
    session_start();
    if(!isset($_SESSION["user_id"])){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "1001"){ 
        header("Location: ../unauthorized.html"); 
        exit(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../styles/voterUI.css">
    <script src="../jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            <a class="items" href="./voterSettings.php">Settings</a>
        </div>
    </div>
    
    <div id="main_content">
        <button id="signout_btn">Sign out</button>
    </div>

    <script src="./voterUtils.js"></script>
</body>
</html>
