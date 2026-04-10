<?php
header('Content-Type: application/json');
session_start();
include_once "../../model/reportModel.php";
include_once "../../model/admin/readOperations.php";
include_once "../../model/admin/createOperations.php";

// --- Controller: Only business logic, no DB or direct response formatting ---

function getUserInfoController() {
    if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        return ["success" => false, "message" => "User not logged in", "data" => (object)[]];
    }
    $username = $_SESSION['username'];
    $user_role = $_SESSION['role'];
    $is_admin = in_array(intval($user_role), [3000, 3001, 3002]);
    return [
        "success" => true,
        "message" => "",
        "data" => [
            "username" => $username,
            "role" => $user_role,
            "is_admin" => $is_admin
        ]
    ];
}

function getElectionController() {
    try {
        $election = getCurrentElection();
    } catch (Exception $e) {
        $election = null;
    }
    if ($election === null) {
        return ["success" => false, "message" => "No election available", "data" => (object)[]];
    }
    return ["success" => true, "message" => "", "data" => $election];
}

function checkElectionStatusController($election, $is_admin) {
    $election_id = $election['election_id'];
    $election_status = $election['status'];
    $is_ongoing = isElectionOngoing($election_status);
    if ($is_ongoing && !$is_admin) {
        return [
            "success" => true,
            "message" => "Election results are not yet available. Results will be displayed after the election closes.",
            "data" => [
                "election_status" => "active",
                "election_title" => $election['election_title'],
                "restricted" => true
            ]
        ];
    }
    return [
        "success" => true,
        "message" => "",
        "data" => [
            "election_status" => $election_status,
            "election_id" => $election_id,
            "restricted" => false
        ]
    ];
}

function fetchResultsController($election_id, $election_status) {
    if ($election_status === 'upcoming') {
        $results = getElectionCandidatesByParty($election_id);
    } else {
        $results = getElectionResults($election_id);
    }
    if (!$results["success"]) {
        return ["success" => false, "message" => $results["message"], "data" => (object)[]];
    }
    return ["success" => true, "message" => "", "data" => $results["data"]];
}

function handleGetResultsController() {
    $userResult = getUserInfoController();
    if (!$userResult["success"]) {
        return ["success" => false, "message" => $userResult["message"], "data" => (object)[]];
    }
    $username = $userResult["data"]["username"];
    $is_admin = $userResult["data"]["is_admin"];

    $electionResult = getElectionController();
    if (!$electionResult["success"]) {
        return [
            "success" => true,
            "message" => $electionResult["message"],
            "data" => [
                "election_status" => "no_elections",
                "username" => $username
            ]
        ];
    }
    $election = $electionResult["data"];

    $statusResult = checkElectionStatusController($election, $is_admin);
    if ($statusResult["data"]["restricted"] ?? false) {
        return [
            "success" => true,
            "message" => $statusResult["message"],
            "data" => [
                "election_status" => "active",
                "username" => $username,
                "election_title" => $election["election_title"]
            ]
        ];
    }

    $election_id = $statusResult["data"]["election_id"];
    $election_status = $statusResult["data"]["election_status"];
    $resultsData = fetchResultsController($election_id, $election_status);
    if (!$resultsData["success"]) {
        return $resultsData;
    }

    $status = $statusResult["data"]["election_status"];

    return [
        "success" => true,
        "message" => "",
        "data" => [
            "election_status" => $status,
            "username" => $username,
            "election_title" => $election["election_title"],
            "positions" => $resultsData["data"]
        ]
    ];
}

try {
    $response = handleGetResultsController();
} catch (Exception $e) {
    error_log("Results retrieval error: " . $e->getMessage());
    $response = [
        "success" => false,
        "message" => "Server error: " . $e->getMessage(),
        "data" => (object)[]
    ];
}
if (isset($response) && !$response["success"]) {
    error_log("Results retrieval failed: " . json_encode($response));
}
echo json_encode($response);
?>