<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include(__DIR__ . "/../../model/voter/readOperations.php");
include(__DIR__ . "/../../model/voter/createOperations.php");

$action = $_POST["action"];

switch($action){
    case "sayHello":
        sayHello();
        break;
    case "getElectionFormDetails":
        getElectionFormDetails();
        break;
    case "submitFormAnswers":
        $user_id = initializeSessionUserId();
        submitFormAnswers($user_id);
        break;
    default:
        echo json_encode([
            "message" => "action doesnt exist"
        ]);
        break;
}

function initializeSessionUserId(){
    $user_id = $_SESSION["user_id"];
    return $user_id;
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

    if($status == "upcoming"){
        echo json_encode([
            "status" => $status
        ]);
        return;
    }

    // get election_id of ongoing election
    $election_details = getDetailsOfActiveElection();
    $election_id = $election_details["election_id"];
    $start_date = $election_details["start_date"];
    $election_year = substr($start_date, 0, 4);

    // check if user has voted and return already_voted
    $user_id = initializeSessionUserId();
    $studentvoter_id = getVoterIdWithUserId($user_id)["studentvoter_id"];

    if(hasUserVoted($election_id, $studentvoter_id)){
        echo json_encode([
            "status" => "already_voted",
            "studentvoter_id" => $studentvoter_id
        ]);
        return;
    }

    // get the positions within that election
    // get the candidates that aligns with the position_id and the election_id
    $positions = getCandidatesByRoleAndParty($election_id);

    
    echo json_encode([
        "status" => $status,
        "election_title" => $election_details["election_title"],
        "election_id" => $election_id,
        "election_year" => $election_year,
        "positions" => $positions
    ]);
    return;
}


function submitFormAnswers($user_id){
    $vote_json = json_decode($_POST["vote_json"], true);

    $studentvoter_id = getVoterIdWithUserId($user_id)["studentvoter_id"];
    $election_id = $vote_json["election_id"];

    foreach($vote_json["positions"] as $position){
        $position_id = $position["position_id"];

        foreach($position["candidates"] as $candidate_id){
            if($candidate_id == '0'){
                insertAbstainVote($studentvoter_id, $position_id, $election_id);
            } else{
                insertVote($studentvoter_id, $candidate_id, $position_id, $election_id);
            }
        }
    }
    

    echo json_encode([
        "status" => "success",
        "vote_json" => $vote_json,
        "studentvoter_id" => $studentvoter_id,
        "election_id" => $election_id,
        "last_position_id" => $position_id
    ]);
    return;
}

?>