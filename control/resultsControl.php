<?php
header('Content-Type: application/json');
include("../model/dbconn.php");
include("../model/readOperations.php");
include("../model/resultsModel.php");
session_start();


function getUserInfo() {
    if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    $username = $_SESSION['username'];
    $user_role = $_SESSION['role'];
    $is_admin = ($user_role === "1000");
    
    return [
        'success' => true,
        'username' => $username,
        'role' => $user_role,
        'is_admin' => $is_admin
    ];
}

function getAvailableElection() {
    $election = getCurrentElection();
    
    if ($election === null) {
        return ['success' => false, 'election' => null];
    }
    
    return ['success' => true, 'election' => $election];
}

function checkElectionStatus($election, $is_admin) {
    $election_id = $election['election_id'];
    $end_date = $election['end_date'];
    $is_ongoing = isElectionOngoing($end_date);
    
    // If ongoing and user is not admin, return wait message
    if ($is_ongoing && !$is_admin) {
        return [
            'status' => 'ongoing_restricted',
            'election_title' => $election['election_title'],
            'message' => 'Election results are not yet available. Results will be displayed after the election closes.'
        ];
    }
    
    return ['status' => $is_ongoing ? 'ongoing' : 'ended', 'election_id' => $election_id];
}

function fetchResults($election_id) {
    $results = getElectionResults($election_id);
    
    if (!$results['success']) {
        return ['success' => false, 'message' => $results['message']];
    }
    
    return ['success' => true, 'data' => $results['data']];
}

function handleGetResults() {
    // Get user information
    $userResult = getUserInfo();
    if (!$userResult['success']) {
        return [
            'success' => false,
            'message' => $userResult['message']
        ];
    }
    
    $username = $userResult['username'];
    $is_admin = $userResult['is_admin'];
    
    // Get available election
    $electionResult = getAvailableElection();
    if (!$electionResult['success']) {
        return [
            'success' => true,
            'election_status' => 'none',
            'username' => $username,
            'message' => 'No election available'
        ];
    }
    
    $election = $electionResult['election'];
    
    // Check election status and access level
    $statusResult = checkElectionStatus($election, $is_admin);
    
    // User does not have access (ongoing but not admin)
    if ($statusResult['status'] === 'ongoing_restricted') {
        return [
            'success' => true,
            'election_status' => 'ongoing',
            'username' => $username,
            'election_title' => $statusResult['election_title'],
            'message' => $statusResult['message']
        ];
    }
    
    // User has access - fetch and return results
    $election_id = $statusResult['election_id'];
    $resultsData = fetchResults($election_id);
    
    if (!$resultsData['success']) {
        return $resultsData;
    }
    
    $status = $statusResult['status'];
    $view_type = ($status === 'ongoing') ? 'admin_live_count' : ($is_admin ? 'admin_results' : 'voter_results');
    
    return [
        'success' => true,
        'election_status' => $status,
        'username' => $username,
        'election_title' => $election['election_title'],
        'view_type' => $view_type,
        'positions' => $resultsData['data']
    ];
}

// Error handling for the entire results retrieval process
try {
    $response = handleGetResults();
} catch (Exception $e) {
    error_log("Results retrieval error: " . $e->getMessage());
    $response = [
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ];
}

// Log all errors
if (isset($response) && !$response['success']) {
    error_log("Results retrieval failed: " . json_encode($response));
}

echo json_encode($response);
?>
