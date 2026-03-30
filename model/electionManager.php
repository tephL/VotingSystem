<?php
include("dbconn.php");


function createElections(){
    global $conn;

    $title = $_POST['title'];
    $status = $_POST['status'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    $stmt = $conn->prepare("INSERT INTO Elections (election_title, status, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $status, $start, $end);

    if($stmt->execute()){
        echo "success";
    }else{
        echo "error";
    }

    $stmt->close();
}



function getElection(){
    global $conn;

    $sql = "SELECT * FROM Elections ORDER BY election_id DESC";
    $result = $conn->query($sql);

    $today = date('Y-m-d');
    $data = array();

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){

            if($today < $row['start_date']){
                $row['status'] = "Upcoming";
            }else if($today >= $row['start_date'] && $today <= $row['end_date']){
                $row['status'] = "Ongoing";
            }else{
                $row['status'] = "Done";
            }

            $data[] = $row;
        }
    }

    echo json_encode($data);
}



function getElectionById(){
    global $conn;

    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM Elections WHERE election_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo json_encode($result->fetch_assoc());
    }else{
        echo "error";
    }

    $stmt->close();
}



function updateElection(){
    global $conn;

    $id = $_POST['id'];
    $title = $_POST['title'];
    $status = $_POST['status'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    $stmt = $conn->prepare("UPDATE Elections SET election_title=?, status=?, start_date=?, end_date=? WHERE election_id=?");
    $stmt->bind_param("ssssi", $title, $status, $start, $end, $id);

    if($stmt->execute()){
        echo "success";
    }else{
        echo "error";
    }

    $stmt->close();
}



function deleteElection(){
    global $conn;

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM Elections WHERE election_id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "success";
    }else{
        echo "error";
    }

    $stmt->close();
}
?>