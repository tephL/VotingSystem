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
    if(!getElectionStatus()){
        echo json_encode([
            "message" => "no ongoing election"
        ]);
        return;
    }

    // get election_id of ongoing election
    $election_id = getIDOfActiveElection();
    echo json_encode([
        "message" => "hi",
        "election_id" => $election_id
    ]);
    return;

    // get the positions within that election
    
    // get the candidates that aligns with the position_id and the election_id
}


?>