<?php

date_default_timezone_set('Asia/Manila');

include("dbconn.php");

function getStatus($start, $end) {
    $now = date('Y-m-d H:i:s');
    $start = date('Y-m-d H:i:s', strtotime($start));
    $end = date('Y-m-d H:i:s', strtotime($end));

    if ($now < $start) {
        return "Upcoming";
    } else if ($now >= $end) { 
        return "Completed";
    } else {
        return "Active";
    }
}

function createElections() {
    global $conn;

    $title = $_POST['title'];
    $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
    $end = date('Y-m-d H:i:s', strtotime($_POST['end']));
    $positions = json_decode($_POST['positions'], true);

    if ($start > $end) {
        echo "invalid";
        return;
    }

    $status = getStatus($start, $end);

    if ($status === "Active") {
        $now = date('Y-m-d H:i:s');
        $check = $conn->query("SELECT * FROM Elections WHERE start_date <= '$now' AND end_date >= '$now'");
        if ($check->num_rows > 0) {
            echo "active";
            return;
        }
    }

    $sql = "INSERT INTO Elections (election_title, status, start_date, end_date)
            VALUES ('$title', '$status', '$start', '$end')";

    if ($conn->query($sql) === TRUE) {
        $election_id = $conn->insert_id;

        foreach ($positions as $p) {
            $posName = $p['name'];
            $posMax = (int) $p['max'];
            $conn->query("INSERT INTO Positions (position_name, max_votes, election_id)
                          VALUES ('$posName', $posMax, $election_id)");
        }

        echo "success";
    } else {
        echo "error";
    }
}

function getElection() {
    global $conn;

    $result = $conn->query("SELECT * FROM Elections ORDER BY election_id DESC");
    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            
            $newStatus = getStatus($row['start_date'], $row['end_date']);

            if ($row['status'] != $newStatus) {
                $id = $row['election_id'];
                $conn->query("UPDATE Elections SET status = '$newStatus' WHERE election_id = $id");
            }

            $row['status'] = $newStatus;
            $data[] = $row;
        }
    }

    echo json_encode($data);
}

function getElectionById() {
    global $conn;

    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM Elections WHERE election_id = $id");

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo "error";
    }
}

function updateElection() {
    global $conn;

    $id = $_POST['id'];
    $title = $_POST['title'];
    $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
    $end = date('Y-m-d H:i:s', strtotime($_POST['end']));

    if ($start > $end) {
        echo "invalid";
        return;
    }

    $status = getStatus($start, $end);

    if ($status === "Active") {
        $now = date('Y-m-d H:i:s');
        $check = $conn->query(
            "SELECT * FROM Elections 
             WHERE start_date <= '$now' 
             AND end_date >= '$now'
             AND election_id != $id"
        );
        if ($check->num_rows > 0) {
            echo "active";
            return;
        }
    }

    $sql = "UPDATE Elections SET 
            election_title = '$title',
            status = '$status',
            start_date = '$start',
            end_date = '$end'
            WHERE election_id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}
function deleteElection() {
    global $conn;

    $id = (int)$_POST['id'];   
    if ($id <= 0) {
        echo "invalid";
        return;
    }
    $conn->begin_transaction();

    try {
        $conn->query("DELETE FROM Votes WHERE election_id = $id");
        $conn->query("DELETE FROM Candidates WHERE election_id = $id");
        $conn->query("DELETE FROM PoliticalParties WHERE election_id = $id");
        $conn->query("DELETE FROM Positions WHERE election_id = $id");
        $conn->query("DELETE FROM Elections WHERE election_id = $id");

        $conn->commit();
        echo "success";

    } catch (Exception $e) {
        $conn->rollback();
        echo "error";
    }
}
function getPositionsByElection($election_id) {
    global $conn;
    
    $sql = "SELECT position_id, position_name, max_votes 
            FROM Positions 
            WHERE election_id = $election_id 
            ORDER BY position_id ASC";
    
    $result = $conn->query($sql);
    $positions = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $positions[] = $row;
        }
    }
    
    echo json_encode($positions);
}
?>
