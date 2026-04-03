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

    return $r_sql->fetch_assoc();
}

function getCandidatesByRoleAndParty($election_id){
    global $conn;

    $sql = "SELECT 
                p.position_id,
                p.position_name,
                p.max_votes,
                c.candidate_id,
                s.first_name,
                s.middle_name,
                s.last_name,
                pp.party_id,
                pp.party_name
            FROM Positions p
            LEFT JOIN Candidates c
                ON c.position_id = p.position_id
                AND c.election_id = p.election_id
            LEFT JOIN Students s
                ON c.student_id = s.student_id
                AND s.student_id = c.student_id
            LEFT JOIN PoliticalParties pp
            	ON c.party_id = pp.party_id
                AND pp.party_id = c.party_id
            WHERE p.election_id = '$election_id'";

    $r_sql = $conn->query($sql);
    return formatCandidatesByRoleAndParty($r_sql);
}


function formatCandidatesByRoleAndParty($r_sql){
    if($r_sql->num_rows < 1) return [];

    $rows = $r_sql->fetch_all(MYSQLI_ASSOC);

    $positions = [];

    foreach($rows as $row){
        $position_id  = $row["position_id"];
        $party_id     = $row["party_id"];
        $candidate_id = $row["candidate_id"];

        // initialize position if not yet seen
        if(!isset($positions[$position_id])){
            $positions[$position_id] = [
                "position_id"   => $position_id,
                "position_name" => $row["position_name"],
                "max_votes"     => $row["max_votes"],  
                "political_parties"    => []
            ];
        }

        // skip if no candidate on this row
        if(!$candidate_id) continue;

        // initialize partylist under this position if not yet seen
        if(!isset($positions[$position_id]["political_parties"][$party_id])){
            $positions[$position_id]["political_parties"][$party_id] = [
                "party_id"   => $party_id,
                "party_name" => $row["party_name"],
                "candidates" => []
            ];
        }

        // append candidate to their partylist
        $positions[$position_id]["political_parties"][$party_id]["candidates"][] = [
            "candidate_id" => $candidate_id,
            "first_name"   => $row["first_name"],
            "middle_name"  => $row["middle_name"],
            "last_name"    => $row["last_name"]
        ];
    }

    // reset to clean arrays 
    foreach($positions as &$position){
        $position["political_parties"] = array_values($position["political_parties"]);
        foreach($position["political_parties"] as &$party){
            $party["candidates"] = array_values($party["candidates"]);
        }
    }

    return array_values($positions);
}


function getVoterIdWithUserId($user_id){
    global $conn;

    $sql = "SELECT studentvoter_id FROM StudentVoters WHERE user_id = '$user_id'";
    $r_sql = $conn->query($sql);
    return $r_sql->fetch_assoc();
}

function hasUserVoted($election_id, $studentvoter_id){
    global $conn;

    $sql = "SELECT * FROM Votes WHERE studentvoter_id = '$studentvoter_id' AND election_id = '$election_id'";
    $r_sql = $conn->query($sql);

    if($r_sql->num_rows > 0){
        return true;
    } else{
        return false;
    }
}


?>