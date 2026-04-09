<?php

include_once(__DIR__ . "/../dbconn.php");

$conn = instantiateDbForUser("root", "");

function insertVote($studentvoter_id, $candidate_id, $position_id, $election_id){
    global $conn;

    $sql = "INSERT INTO Votes(studentvoter_id, candidate_id, position_id, election_id) VALUES ('$studentvoter_id', '$candidate_id', '$position_id', '$election_id')";
    $conn->query($sql);
}

function insertAbstainVote($studentvoter_id, $position_id, $election_id){
    global $conn;

    $sql = "INSERT INTO Votes(studentvoter_id, candidate_id, position_id, election_id) VALUES ('$studentvoter_id', NULL, '$position_id', '$election_id')";
    $conn->query($sql);
};


?>