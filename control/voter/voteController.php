<?php

include(__DIR__ . "/../../model/voter/readOperations.php");

$action = $_POST["action"];

switch($action){
    case "sayHello":
        sayHello();
        break;
    case "getElectionFormDetails":
        getElectionFormDetails();
        break;
    default:
        echo "action doesnt exist";
        break;
}

function sayHello(){
    echo json_encode([
        "message" => "Hello!"
    ]);
    return;
}

function getElectionFormDetails(){
    // check if there are active elections
    $status = getElectionStatus();

    if($status == "completed"){
        echo json_encode([
            "status" => $status
        ]);
        return;
    }

    // get election_id of ongoing election
    $election_details = getDetailsOfActiveElection();

    // get the positions within that election
    // get the candidates that aligns with the position_id and the election_id
    $candidates = getCandidatesOfElection($election_id);
    
    echo json_encode([
        "status" => $status,
        "election_title" => $election_details[0]["election_title"],
        "candidates" => $candidates
    ]);
    return;
}


?>