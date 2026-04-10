<?php 
    session_start();
    if (!isset($_SESSION["user_id"])) { 
        header("Location: ../../index.html"); 
        exit(); 
    }

    if($_SESSION["role"] != "3001"){ 
        header("Location: ../../index.html"); 
        exit(); 
    }

    $role_id = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/mainUI.css">
    <link rel="stylesheet" href="./styles/candidatesManager.css">
    <script src="../../src/jquery.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates Manager</title>
</head>
<body>

    <div id="sidebar">
        <div id="title">
            <h1>UniVote</h1>
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

    <div id="main_content" class="candidates-page">

      
        <div id="election_select_section">
            <label for="election_dropdown">Select Election</label>
            <select id="election_dropdown">
                <option value="">-- Loading elections... --</option>
            </select>
        </div>

        <div id="view_toggle_section">
            <button id="btn_student_view" class="view_toggle_btn">Student List</button>
            <button id="btn_manage_view" class="view_toggle_btn active">Manage Candidates</button>
            <button id="btn_slate_view"  class="view_toggle_btn">View Slate</button>
        </div>
       
        <div id="manage_view">

<<<<<<< HEAD
=======
            <h3>Party List</h3>
>>>>>>> MigrationElectCandidate
            <div id="party_tabs_section">
                <div id="party_tabs_container"></div>
            </div>

<<<<<<< HEAD
=======
            <h3>Positions</h3>
>>>>>>> MigrationElectCandidate
            <div id="tabs_section">
                <div id="tabs_container"></div>
            </div>

            <div id="candidates_section">
<<<<<<< HEAD
                <h3>Candidates</h3>
=======
                <h3>Current Candidates</h3>
>>>>>>> MigrationElectCandidate
                <div id="candidates_list"></div>
            </div>

            <div id="add_candidate_section">
                <h3>Add a Candidate</h3>
                <input type="text" id="student_search_input" placeholder="Search student by name or ID...">
                <select id="student_dropdown"></select>
                <div id="selected_student_display">No student selected.</div>
                <button id="add_candidate_btn">Add Candidate</button>
                <div id="status_message"></div>
            </div>

        </div>


        <div id="student_view">
            <h3>Student List</h3>

            <table id="student_reference_table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

       
        <div id="slate_view">
            <div id="slate_container"></div>
        </div>

    </div>
    <script src="./scripts/adminUtils.js"></script>
    <script src="./scripts/candidatesManager.js"></script>
</body>
</html>
