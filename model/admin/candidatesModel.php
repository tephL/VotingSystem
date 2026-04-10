<?php

function fetchAll($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function syncElectionStatuses($conn) {
    $conn->query("UPDATE Elections SET status = 'upcoming'  WHERE NOW() < start_date");
    $conn->query("UPDATE Elections SET status = 'active'    WHERE NOW() >= start_date AND NOW() <= end_date");
    $conn->query("UPDATE Elections SET status = 'completed' WHERE NOW() > end_date");
}
// ELECTIONS
function getElections($conn) {
    syncElectionStatuses($conn);
    $query = "
        SELECT election_id, election_title 
        FROM Elections 
        WHERE status != 'complete'
        ORDER BY election_id DESC
    ";
    $result = $conn->query($query);
    return fetchAll($result);
}

// POSITIONS
function getPositionsByElection($conn, $election_id) {
    $query = "
        SELECT position_id, position_name, max_votes 
        FROM Positions 
        WHERE election_id = '$election_id'
        ORDER BY position_id ASC
    ";
    $result = $conn->query($query);
    return fetchAll($result);
}

// PARTIES
function getPartiesByElection($conn, $election_id) {
    $query = "
        SELECT party_id, party_name 
        FROM PoliticalParties 
        WHERE election_id = '$election_id' AND status = 'active'
        ORDER BY party_name ASC
    ";
    $result = $conn->query($query);
    return fetchAll($result);
}

// CANDIDATES
function getCandidatesByPosition($conn, $position_id) {
    $query = "
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
    ";
    $result = $conn->query($query);
    return fetchAll($result);
}

function getCandidatesByPartyAndPosition($conn, $party_id, $position_id) {
    $query = "
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
    ";
    $result = $conn->query($query);
    return fetchAll($result);
}

// SLATE
function getSlate($conn, $election_id) {

    $parties_query = "
        SELECT party_id, party_name
        FROM PoliticalParties
        WHERE election_id = '$election_id' AND status = 'active'
        ORDER BY party_name ASC
    ";
    $parties = fetchAll($conn->query($parties_query));

    $positions_query = "
        SELECT position_id, position_name
        FROM Positions
        WHERE election_id = '$election_id'
        ORDER BY position_id ASC
    ";
    $positions = fetchAll($conn->query($positions_query));

    $candidates_query = "
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
    ";
    $candidates = fetchAll($conn->query($candidates_query));

    return [
        "parties" => $parties,
        "positions" => $positions,
        "candidates" => $candidates
    ];
}

// SEARCH
function searchStudents($conn, $term, $election_id) {
    $query = "
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
    ";
    $result = $conn->query($query);
    return fetchAll($result);
}

// ADD
function addCandidate($conn, $student_id, $position_id, $party_id, $election_id) {

    $limitRes = $conn->query("
        SELECT max_votes FROM Positions WHERE position_id = '$position_id'
    ");

    if (!$limitRes || $limitRes->num_rows === 0) {
        return ["success" => false, "message" => "Position not found."];
    }

    $row = $limitRes->fetch_assoc();
    $max_votes = $row['max_votes'];


    $countRes = $conn->query("
        SELECT COUNT(*) as total FROM Candidates
        WHERE party_id = '$party_id' AND position_id = '$position_id'
    ");

    $countRow = $countRes->fetch_assoc();
    $current = $countRow['total'];

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

    if ($ok) {
        return ["success" => true, "message" => "Candidate added successfully."];
    } else {
        return ["success" => false, "message" => "Failed to add candidate."];
    }
}

// REMOVE
function removeCandidate($conn, $candidate_id) {

    $check = $conn->query("SELECT vote_id FROM Votes WHERE candidate_id = '$candidate_id' LIMIT 1");

    if ($check && $check->num_rows > 0) {
        return ["success" => false, "message" => "Cannot remove. This candidate already has votes."];
    }

    $ok = $conn->query("DELETE FROM Candidates WHERE candidate_id = '$candidate_id'");

    if ($ok) {
        return ["success" => true, "message" => "Candidate removed successfully."];
    } else {
        return ["success" => false, "message" => "Failed to remove candidate."];
    }
}
?>
