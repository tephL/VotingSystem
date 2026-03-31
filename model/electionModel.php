<?php

include("dbconn.php");


function getStatus($start, $end){
    $today = date('Y-m-d');
    $start = date('Y-m-d', strtotime($start));
    $end = date('Y-m-d', strtotime($end));

    if($today < $start){
        return "Upcoming";
    }
    else if($today > $end){
        return "Done";
    }
    else{
        return "Active";
    }
}

function createElections(){
    global $conn;

    $title = $_POST['title'];
    $start = date('Y-m-d', strtotime($_POST['start']));
    $end = date('Y-m-d', strtotime($_POST['end']));

    if($start > $end){
        echo "invalid";
        return;
    }

    $status = getStatus($start, $end);
    if($status === "Active"){
        $today = date('Y-m-d');
        $check = $conn->query("SELECT * FROM Elections WHERE start_date <= '$today' AND end_date >= '$today'");
        
        if($check->num_rows > 0){
            echo "active";
            return;
        }
    }

    $sql = "INSERT INTO Elections (election_title, status, start_date, end_date)
            VALUES ('$title', '$status', '$start', '$end')";

    if($conn->query($sql) === TRUE){
        echo "success";
    }else{
        echo "error";
    }
}

//get the whole table
function getElection(){
    global $conn;

    $result = $conn->query("SELECT * FROM Elections ORDER BY election_id DESC");
    $data = array();

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
        $newStatus = getStatus($row['start_date'], $row['end_date']);

        //auto updating status in db
        if($row['status'] != $newStatus){
        $id = $row['election_id'];
        $conn->query("UPDATE Elections SET status = '$newStatus' WHERE election_id = $id");
        }

        // status dor ui
        $row['status'] = $newStatus;

        $data[] = $row;
        }
    }

    echo json_encode($data);
}
//for editing
function getElectionById(){
    global $conn;

    $id = $_GET['id'];

    $result = $conn->query("SELECT * FROM Elections WHERE election_id = $id");

    if($result->num_rows > 0){
        echo json_encode($result->fetch_assoc());
    }else{
        echo "error";
    }
}

function updateElection(){
    global $conn;

    $id = $_POST['id'];
    $title = $_POST['title'];
    $start = date('Y-m-d', strtotime($_POST['start']));
    $end = date('Y-m-d', strtotime($_POST['end']));

    if($start > $end){
        echo "invalid";
        return;
    }

    $status = getStatus($start, $end);
    if($status === "Active"){
        $today = date('Y-m-d');
        $check = $conn->query
        ("SELECT * FROM Elections 
         WHERE start_date <= '$today' 
        AND end_date >= '$today'
        AND election_id != $id");

        if($check->num_rows > 0){
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

    if($conn->query($sql) === TRUE){
        echo "success";
    }else{
        echo "error";
    }
}

function deleteElection(){
    global $conn;

    $id = $_POST['id'];
    $sql = "DELETE FROM Elections WHERE election_id = $id";

    if($conn->query($sql) === TRUE){
        echo "success";
    }else{
        echo "error";
    }
}
?>
