<?php 
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
    <link rel="stylesheet" href="styles/electionManager.css">
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
            <a class="items" href="./dashboard.php">Dashboard</a>
            <a class="items" href="./electionManager.php">Election Manager</a>
            <a class="items" href="./candidatesManager.php">Candidates Manager</a>
            <a class="items" href="./votersManager.php">Voters Manager</a>
            <a class="items" href="./adminSettings.php">Settings</a>
        </div>
    </div>
    
    <div id="main_content">
        <!-- Election List Panel -->
        <div id="election-panel"> 
            <div id="election-title">
                <p>Election List</p>
                <button type="button" id="createbutton">+ Create</button>
            </div>
            <div id="history">
                <ul>
                    <li>Title</li>
                    <li>Status</li>
                    <li>Start Date</li>
                    <li>End Date</li>
                    <li>Actions</li>
                </ul>
                <hr>
                <div id="history-list"></div>
            </div> 
        </div>

        <!-- Create / Edit Election Panel -->
        <div id="create-panel">
            <div id="create-list">
                <h1>Create Election</h1>
                <hr>
                <div id="create-title">
                    <p id="elect-title">Election Title</p>
                    <input type="text" id="title-input" placeholder="e.g Student Council Election">
                </div>
                
                <div id="create-date">
                    <div>
                        <label>Start Date</label>
                        <input type="datetime-local" id="start-date" name="start">
                    </div>
                    <div>
                        <label>End Date</label>
                        <input type="datetime-local" id="end-date" name="end">
                    </div>
                </div>

                <!-- Positions -->
                <div> 
                    <p>Position</p>
                    <div class="create-box" id="positions-box">
                        <div class="position-row">
                            <label>Position Name</label>
                            <input type="text" class="pos-name" placeholder="e.g President">
                            <label>Max Votes</label>
                            <input type="number" class="pos-max" step="1" min="1" max="8">
                        </div>
                    </div>
                    <button type="button" id="add-position-btn">+ Add Position</button>
                </div>

                <!-- Parties -->
                <div>
                    <p>Political Parties</p>
                    <div class="create-box" id="parties-box">
                        <div class="party-row">
                            <label>Party Name</label>
                            <input type="text" class="party-name" placeholder="e.g Partido Uno">
                            <button type="button" class="remove-party-btn">Remove</button>
                        </div>
                    </div>
                    <button type="button" id="add-party-btn">+ Add Party</button>
                </div>
                    
                <div id="create-btns">
                    <button type="button" id="create-btn">Create</button> 
                    <button type="button" id="cancel-btn">Cancel</button> 
                </div>
            </div>
        </div>
    </div>

    <script src="./scripts/adminUtils.js"></script>
    <script src="./scripts/electionManager.js"></script>
</body>
</html>