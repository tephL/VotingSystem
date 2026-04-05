<?php

function fetchAll($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// ELECTIONS
function getElections($conn) {
    return fetchAll($conn->query("
        SELECT election_id, election_title 
        FROM Elections 
        WHERE status != 'Completed'
        ORDER BY election_id DESC
    "));
}

// POSITIONS
function getPositionsByElection($conn, $election_id) {
    return fetchAll($conn->query("
        SELECT position_id, position_name, max_votes 
        FROM Positions 
        WHERE election_id = '$election_id'
        ORDER BY position_id ASC
    "));
}

// PARTIES
function getPartiesByElection($conn, $election_id) {
    return fetchAll($conn->query("
        SELECT party_id, party_name 
        FROM PoliticalParties 
        WHERE election_id = '$election_id' AND status = 'active'
        ORDER BY party_name ASC
    "));
}

// CANDIDATES
function getCandidatesByPosition($conn, $position_id) {
    return fetchAll($conn->query("
        SELECT 
            c.candidate_id,
            c.student_id,
            c.party_id,
            s.first_name,
            s.last_name,
            p.party_name
        FROM Candidates c
        JOIN Students s ON c.student_id = s.student_id
        JOIN PoliticalParties p ON c.party_id = p.party_id
        WHERE c.position_id = '$position_id'
        ORDER BY s.last_name ASC
    "));
}

function getCandidatesByPartyAndPosition($conn, $party_id, $position_id) {
    return fetchAll($conn->query("
        SELECT 
            c.candidate_id,
            c.student_id,
            c.party_id,
            c.position_id,
            s.first_name,
            s.last_name
        FROM Candidates c
        JOIN Students s ON c.student_id = s.student_id
        WHERE c.party_id = '$party_id' 
        AND c.position_id = '$position_id'
        ORDER BY s.last_name ASC
    "));
}

// SLATE
function getSlate($conn, $election_id) {

    $parties = fetchAll($conn->query("
        SELECT party_id, party_name
        FROM PoliticalParties
        WHERE election_id = '$election_id' AND status = 'active'
        ORDER BY party_name ASC
    "));

    $positions = fetchAll($conn->query("
        SELECT position_id, position_name
        FROM Positions
        WHERE election_id = '$election_id'
        ORDER BY position_id ASC
    "));

    $candidates = fetchAll($conn->query("
        SELECT 
            c.candidate_id,
            c.party_id,
            c.position_id,
            c.student_id,
            s.first_name,
            s.middle_name,
            s.last_name,
            col.college_name
        FROM Candidates c
        JOIN Students s ON c.student_id = s.student_id
        JOIN College col ON s.college_id = col.college_id
        WHERE c.election_id = '$election_id'
        ORDER BY s.last_name ASC
    "));

    return [
        "parties" => $parties,
        "positions" => $positions,
        "candidates" => $candidates
    ];
}

// SEARCH
function searchStudents($conn, $term, $election_id) {
    return fetchAll($conn->query("
        SELECT s.student_id, s.first_name, s.last_name
        FROM Students s
        WHERE (
            s.first_name LIKE '%$term%' OR
            s.middle_name LIKE '%$term%' OR
            s.last_name LIKE '%$term%' OR
            s.student_id LIKE '%$term%'
        )
        AND s.student_id NOT IN (
            SELECT student_id FROM Candidates WHERE election_id = '$election_id'
        )
        ORDER BY s.last_name ASC
    "));
}

// ADD
function addCandidate($conn, $student_id, $position_id, $party_id, $election_id) {

    // Get max_votes for this position
    $limitRes = $conn->query("
        SELECT max_votes FROM Positions WHERE position_id = '$position_id'
    ");
    if (!$limitRes || $limitRes->num_rows === 0) {
        return ["success" => false, "message" => "Position not found."];
    }
    $max_votes = (int)$limitRes->fetch_assoc()['max_votes'];

    // Count existing candidates for this party + position
    $countRes = $conn->query("
        SELECT COUNT(*) as total FROM Candidates
        WHERE party_id = '$party_id' AND position_id = '$position_id'
    ");
    $current = (int)$countRes->fetch_assoc()['total'];

    if ($current >= $max_votes) {
        return [
            "success" => false,
            "message" => "Slot full. This position only allows $max_votes candidate(s) per party."
        ];
    }

    $ok = $conn->query("
        INSERT INTO Candidates (party_id, student_id, election_id, position_id)
        VALUES ('$party_id', '$student_id', '$election_id', '$position_id')
    ");

    return $ok
        ? ["success" => true, "message" => "Candidate added successfully."]
        : ["success" => false, "message" => "Failed to add candidate."];
}

// REMOVE
function removeCandidate($conn, $candidate_id) {

    $check = $conn->query("SELECT vote_id FROM Votes WHERE candidate_id = '$candidate_id' LIMIT 1");

    if ($check && $check->num_rows > 0) {
        return ["success" => false, "message" => "Cannot remove. This candidate already has votes."];
    }

    $ok = $conn->query("DELETE FROM Candidates WHERE candidate_id = '$candidate_id'");

    return $ok
        ? ["success" => true, "message" => "Candidate removed successfully."]
        : ["success" => false, "message" => "Failed to remove candidate."];
}
?>
