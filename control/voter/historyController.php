<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();


include(__DIR__ . "/../../model/voter/historyModel.php");


$action = $_POST["action"];
switch($action){
    case "getMyVotingHistory":
        $user_id = initializeSessionUserId();
        returnVotingHistory($user_id);
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


function returnVotingHistory($user_id){
    $result = getVotesOfAUser($user_id);

    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $election_id = $row['election_id'];
            $position_id = $row['position_id'];
            $party_name = $row['party_name'] ?? 'Independent';

            // ======================
            // CREATE ELECTION LEVEL
            // ======================
            if (!isset($data[$election_id])) {
                $data[$election_id] = [
                    "election_title" => $row["election_title"],
                    "positions" => []
                ];
            }

            // ======================
            // CREATE POSITION LEVEL
            // ======================
            if (!isset($data[$election_id]["positions"][$position_id])) {
                $data[$election_id]["positions"][$position_id] = [
                    "position_name" => $row["position_name"],
                    "parties" => []
                ];
            }

            // ======================
            // CREATE PARTY LEVEL
            // ======================
            if (!isset($data[$election_id]["positions"][$position_id]["parties"][$party_name])) {
                $data[$election_id]["positions"][$position_id]["parties"][$party_name] = [
                    "party_name" => $party_name,
                    "candidates" => []
                ];
            }

            // ======================
            // ADD CANDIDATE
            // ======================
            if ($row["candidate_name"]) {
                $data[$election_id]["positions"][$position_id]["parties"][$party_name]["candidates"][] = [
                    "student_id" => $row["student_id"],
                    "candidate_name" => $row["candidate_name"],
                    "vote_date" => $row["vote_date"]
                ];
            }
        }
    }

    // OPTIONAL: convert associative arrays to clean JSON arrays
    $formatted = array_values(array_map(function ($election) {

        $positions = array_values(array_map(function ($position) {

            $parties = array_values(array_map(function ($party) {
                return $party;
            }, $position["parties"]));

            $position["parties"] = $parties;
            return $position;

        }, $election["positions"]));

        $election["positions"] = $positions;
        return $election;

    }, $data));

    echo json_encode([
        "success" => true,
        "total_elections" => count($formatted),
        "data" => $formatted
    ]);
}
?>