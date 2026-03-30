<?php

include(__DIR__ . "/../dbconn.php");

function getElectionStatus(){
    global $conn;

    $sql = "SELECT * FROM `Elections` WHERE status = 'active'";
    $r_sql = $conn->query($sql);
    
    if($r_sql->num_rows > 0) return true;
    return false;
}

function getIDOfActiveElection(){
    global $conn;

    $sql = "SELECT * FROM `Elections` WHERE status = 'active'";
    $r_sql = $conn->query($sql);
    
    while($row = $r_sql->fetch_assoc()){
        $election_id = $row["election_id"];
    }

    return $election_id;
}

function getPositionsOfElection($election_id){
    global $conn;
}


?>