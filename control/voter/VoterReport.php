<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
session_start();

include_once(__DIR__ . "/../../model/voter/voterReportModel.php");
include_once(__DIR__ . "/../../model/admin/readOperations.php");
include_once(__DIR__ . "/../../model/admin/candidatesModel.php");

// --- Controller: Only business logic, no DB or direct response formatting ---

function getUserInfoController() {
    if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        return ["success" => false, "message" => "User not logged in", "data" => (object)[]];
    }
    $username = $_SESSION['username'];
    $user_role = $_SESSION['role'];
    return [
        "success" => true,
        "message" => "",
        "data" => [
            "username" => $username,
            "role" => $user_role
        ]
    ];
}

function getVoterElectionController() {
    try {
        $election = getVoterCurrentElection();
        
        if ($election === null) {
            return ["success" => false, "message" => "No election available", "data" => (object)[]];
        }
        
        return ["success" => true, "message" => "", "data" => $election];
    } catch (Exception $e) {
        return ["success" => false, "message" => $e->getMessage(), "data" => (object)[]];
    }
}

function fetchResultsController($election_id, $election_status) {
    $resultsData = getVoterElectionResults($election_id, $election_status);
    if (!$resultsData["success"]) {
        return ["success" => false, "message" => $resultsData["message"], "data" => (object)[]];
    }
    return ["success" => true, "message" => "", "data" => $resultsData["data"]];
}

function handleGetResultsController() {
    global $conn;
    syncElectionStatuses($conn);
    
    $userResult = getUserInfoController();
    if (!$userResult["success"]) {
        return ["success" => false, "message" => $userResult["message"], "data" => (object)[]];
    }
    $username = $userResult["data"]["username"];

    $electionResult = getVoterElectionController();
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
    $election_id = intval($election['election_id']);
    $election_status = $election['status'];

    $resultsData = fetchResultsController($election_id, $election_status);
    if (!$resultsData["success"]) {
        return $resultsData;
    }

    return [
        "success" => true,
        "message" => "",
        "data" => [
            "election_status" => $election_status,
            "username" => $username,
            "election_title" => $election["election_title"],
            "positions" => $resultsData["data"]
        ]
    ];
}

try {
    error_log("VoterReport.php - Starting handleGetResultsController");
    $response = handleGetResultsController();
    error_log("VoterReport.php - Response: " . json_encode($response));
} catch (Throwable $e) {
    error_log("VoterReport.php - Fatal error: " . $e->getMessage() . " | " . $e->getTraceAsString());
    $response = [
        "success" => false,
        "message" => "Server error: " . $e->getMessage(),
        "data" => (object)[]
    ];
}
if (isset($response) && !$response["success"]) {
    error_log("VoterReport.php - Results retrieval failed: " . json_encode($response));
}
error_log("VoterReport.php - Echoing response: " . json_encode($response));
echo json_encode($response);
?>
