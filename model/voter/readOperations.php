<?php

include(__DIR__ . "/../dbconn.php");

function getElectionStatus(){
    global $conn;

    $sql = "SELECT status FROM `Elections`";
    $r_sql = $conn->query($sql);
    
    if($r_sql->num_rows > 0){

        $rows = $r_sql->fetch_all(MYSQLI_ASSOC); 

        foreach($rows as $row){
            if($row["status"] == "active") return "active";
        }
        foreach($rows as $row){
            if($row["status"] == "upcoming") return "upcoming";
        }
        foreach($rows as $row){
            if($row["status"] == "completed") return "completed";
        }

    } else{
        return "null";
    }
}

function getDetailsOfActiveElection(){
    global $conn;

    $sql = "SELECT * FROM `Elections` WHERE status = 'active'";
    $r_sql = $conn->query($sql);
    
    while($row = $r_sql->fetch_assoc()){
        $election_id = $row["election_id"];
        $election_title = $row["election_title"];

        $election_details = array([
            "election_id" => $election_id,
            "election_title" => $election_title
        ]);
    }

    return $election_details;
}

function getCandidatesOfElection($election_id){
    global $conn;

    /* [
        [position_id, position_name, candidate_id, candidate_name],
        ]

    */

    $candidates = array();

    $sql = "SELECT 
                p.position_id,
                p.position_name,
                c.candidate_id,
                s.first_name,
                s.middle_name,
                s.last_name
            FROM Positions p
            LEFT JOIN Candidates c
                ON c.position_id = p.position_id
                AND c.election_id = p.election_id
            LEFT JOIN Students s
                ON c.student_id = s.student_id
                AND s.student_id = c.student_id
            WHERE p.election_id = 1000";
    $r_sql = $conn->query($sql);
    
    while($row = $r_sql->fetch_assoc()){
        $position_id = $row["position_id"];
        $position_name = $row["position_name"];
        $candidate_id = $row["candidate_id"];

        $first_name = $row["first_name"];
        $middle_name = $row["middle_name"];
        $last_name = $row["last_name"];

        $candidate_name = array(
            "first_name"=>$first_name,
            "middle_name"=>$middle_name,
            "last_name"=>$last_name
        );

        $candidate = array($position_id, $position_name, $candidate_id, $candidate_name);

        array_push($candidates, $candidate);
    }


    return $candidates;
}



?>