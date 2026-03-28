<?php
header('Content-Type: application/json');
include("../model/dbconn.php");
include("../model/electionModel.php");
session_start();



function getVotesFromPost() {
    $votesRaw = isset($_POST['votes']) ? $_POST['votes'] : [];
    
    if (!is_array($votesRaw) || empty($votesRaw)) {
        return ['success' => false, 'message' => 'No votes', 'votes' => null];
    }
    
    $votes = array_map('decodeVoteString', $votesRaw);
    return ['success' => true, 'votes' => $votes];
}

function getStudentVoterId() {
    if (isset($_SESSION['studentvoter_id'])) {
        return ['success' => true, 'voter_id' => intval($_SESSION['studentvoter_id'])];
    }
    
    if (isset($_SESSION['user_id'])) {
        include("../model/readOperations.php");
        $studentvoter_id = getStudentVoterId($_SESSION['user_id']);

        if ($studentvoter_id === null) {
            return ['success' => false, 'message' => 'User is not registered as a student voter'];
        }
        
        return ['success' => true, 'voter_id' => $studentvoter_id];
    }
    
    return ['success' => false, 'message' => 'User not logged in'];
}

function handleSubmitVote($conn) {

    // Get votes from POST
    $votesResult = getVotesFromPost();
    if (!$votesResult['success']) {
        return $votesResult;
    }
    $votes = $votesResult['votes'];

    // Get student voter ID
    $voterResult = getStudentVoterId();
    if (!$voterResult['success']) {
        return $voterResult;
    }
    $studentvoter_id = $voterResult['voter_id'];

    // Process votes
    $result = processVotes($studentvoter_id, $votes);
    if (!$result['success']) {
        return $result;
    }
    $voted_positions = $result['voted_positions'];
    $vote_counts = $result['vote_counts'] ?? [];

    // Validate required positions
    if (!validateRequiredPositions($voted_positions)) {
        return ['success' => false, 'message' => 'Please vote or abstain for all required positions'];
    }

    // Auto-abstain unused positions and unfilled senator slots
    autoAbstainUnusedPositions($studentvoter_id, $voted_positions, $vote_counts);

    // Mark voter as voted
    markVoterAsVoted($studentvoter_id);
    $_SESSION['has_voted'] = true;

    return ['success' => true, 'votes_count' => count($votes)];
}

//error handling for the entire submission process
try {
    $response = handleSubmitVote($conn);
} catch (Exception $e) {
    error_log("Vote submission error: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
}

// Log all errors
if (isset($response) && !$response['success']) {
    error_log("Vote submission failed: " . json_encode($response));
}

echo json_encode($response);


?>
