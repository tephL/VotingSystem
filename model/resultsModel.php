<?php

include(__DIR__ . "/dbconn.php");

function getCurrentElection() {
    global $conn;
    $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections ORDER BY election_id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return null;
    }
    
    return mysqli_fetch_assoc($result);
}

function isElectionOngoing($end_date) {
    $current_time = new DateTime('now');
    $election_end = new DateTime($end_date);
    return $current_time < $election_end;
}

function getPositions($election_id) {
    global $conn;
    $eid = intval($election_id);
    $sql = "SELECT position_id, position_name FROM Positions WHERE election_id = $eid ORDER BY position_id ASC";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        error_log("Query error: " . mysqli_error($conn));
        return null;
    }
    
    $positions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $positions[] = $row;
    }
    
    return $positions;
}

function calculateVotePercentage(&$candidates) {
    $total_votes = 0;
    foreach ($candidates as $candidate) {
        $total_votes += intval($candidate['vote_count']);
    }
    
    if ($total_votes > 0) {
        foreach ($candidates as &$candidate) {
            $candidate['percentage'] = round((intval($candidate['vote_count']) / $total_votes) * 100, 1);
        }
    }
}

function getCandidates($position_id, $election_id) {
    global $conn;
    $pid = intval($position_id);
    $eid = intval($election_id);
    
    $sql = "
        SELECT 
            c.candidate_id,
            CONCAT(s.first_name, ' ', IFNULL(CONCAT(s.middle_name, ' '), ''), s.last_name) AS candidate_name,
            COUNT(v.vote_id) AS vote_count
        FROM Candidates c
        JOIN Students s ON c.student_id = s.student_id
        LEFT JOIN Votes v ON c.candidate_id = v.candidate_id AND v.position_id = c.position_id AND v.election_id = $eid
        WHERE c.position_id = $pid
        GROUP BY c.candidate_id
        ORDER BY vote_count DESC, c.candidate_id ASC
    ";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("Query error: " . mysqli_error($conn));
        return null;
    }
    
    $candidates = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $candidates[] = $row;
    }
    
    calculateVotePercentage($candidates);
    
    return $candidates;
}

function getElectionResults($election_id) {
    $eid = intval($election_id);
    
    $positions = getPositions($eid);
    if ($positions === null) {
        return ['success' => false, 'message' => 'No positions found'];
    }
    
    $positions_data = [];
    foreach ($positions as $position) {
        $position_id = intval($position['position_id']);
        $position_name = $position['position_name'];
        
        $candidates = getCandidates($position_id, $eid);
        if ($candidates === null) {
            return ['success' => false, 'message' => 'Error retrieving candidates'];
        }
        
        $positions_data[$position_name] = $candidates;
    }
    
    return ['success' => true, 'data' => $positions_data];
}

?>

