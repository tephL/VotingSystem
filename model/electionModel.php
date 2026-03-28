<?php

function decodeVoteString($voteStr) {
    return json_decode($voteStr, true);
}

function insertVote($studentvoter_id, $candidate_id, $position_id) {
    global $conn;
    $pid = intval($position_id);
    $svid = intval($studentvoter_id);
    
    // Handle NULL for abstain votes (when candidate_id is null)
    $cid_val = ($candidate_id === null || $candidate_id === '') ? 'NULL' : intval($candidate_id);
    
    $sql = "INSERT INTO Votes (studentvoter_id, candidate_id, position_id, vote_date) VALUES ($svid, $cid_val, $pid, NOW())";
    
    return mysqli_query($conn, $sql);
}

function processVotes($studentvoter_id, $votes) {
    global $conn;
    $voted_positions = [];
    $vote_counts = []; // Track count of votes per position
    
    foreach ($votes as $vote) {
        $cid = $vote['candidate_id'];
        $pid = $vote['position_id'];
        
        if (!insertVote($studentvoter_id, $cid, $pid)) {
            return ['success' => false, 'message' => 'Error inserting vote'];
        }
        
        $voted_positions[$pid] = true;
        // Count votes per position (only count non-null/non-abstain votes)
        if ($cid !== null && $cid !== '') {
            $vote_counts[$pid] = ($vote_counts[$pid] ?? 0) + 1;
        }
    }
    
    return ['success' => true, 'voted_positions' => $voted_positions, 'vote_counts' => $vote_counts];
}

function validateRequiredPositions($voted_positions) {
    $required_positions = [1000,1001,1002,1003];
    
    foreach ($required_positions as $pos_id) {
        if (!isset($voted_positions[$pos_id])) {
            return false;
        }
    }
    
    return true;
}

function autoAbstainUnusedPositions($studentvoter_id, $voted_positions, $vote_counts = []) {
    global $conn;
    $svid = intval($studentvoter_id);
    
    // Handle senators: max 4 votes allowed
    // Insert abstain votes for unused slots
    $senator_pos_id = 1002;
    $max_senator_votes = 4;
    
    // Get current senator vote count
    $senator_vote_count = $vote_counts[$senator_pos_id] ?? 0;
    $unused_slots = $max_senator_votes - $senator_vote_count;
    
    // Insert abstain votes for unused slots
    for ($i = 0; $i < $unused_slots; $i++) {
        $sql = "INSERT INTO Votes (studentvoter_id, candidate_id, position_id, vote_date) VALUES ($svid, NULL, $senator_pos_id, NOW())";
        mysqli_query($conn, $sql);
    }
}

function markVoterAsVoted($studentvoter_id) {
    global $conn;
    $svid = intval($studentvoter_id);
    $sql = "UPDATE StudentVoters SET has_voted = 1 WHERE studentvoter_id = $svid";
    return mysqli_query($conn, $sql);
}
