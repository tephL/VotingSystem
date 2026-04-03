<?php
// candidatesModel.php - Pure procedural version

function getElections($conn) {
    $sql = "SELECT election_id, election_title FROM Elections ORDER BY election_id DESC";
    $result = $conn->query($sql);
    $elections = array();
    while ($row = $result->fetch_assoc()) { $elections[] = $row; }
    return $elections;
}

function getPositionsByElection($conn, $election_id) {
    $sql = "SELECT position_id, position_name, max_votes FROM Positions WHERE election_id = '$election_id' ORDER BY position_id ASC";
    $result = $conn->query($sql);
    $positions = array();
    while ($row = $result->fetch_assoc()) { $positions[] = $row; }
    return $positions;
}

function getCandidatesByPosition($conn, $position_id) {
    $sql = "SELECT c.candidate_id, c.student_id, c.party_id, s.first_name, s.last_name, p.party_name
            FROM Candidates c
            JOIN Students s ON c.student_id = s.student_id
            JOIN PoliticalParties p ON c.party_id = p.party_id
            WHERE c.position_id = '$position_id'
            ORDER BY s.last_name ASC";
    $result = $conn->query($sql);
    $candidates = array();
    while ($row = $result->fetch_assoc()) { $candidates[] = $row; }
    return $candidates;
}

function getCandidatesByPartyAndPosition($conn, $party_id, $position_id) {
    $sql = "SELECT c.candidate_id, c.student_id, c.party_id, c.position_id, s.first_name, s.last_name
            FROM Candidates c
            JOIN Students s ON c.student_id = s.student_id
            WHERE c.party_id = '$party_id' AND c.position_id = '$position_id'
            ORDER BY s.last_name ASC";
    $result = $conn->query($sql);
    $candidates = array();
    while ($row = $result->fetch_assoc()) { $candidates[] = $row; }
    return $candidates;
}

function getSlate($conn, $election_id) {
    $partiesResult = $conn->query("SELECT party_id, party_name FROM PoliticalParties WHERE election_id = '$election_id' AND status = 'active' ORDER BY party_name ASC");
    $parties = array();
    while ($row = $partiesResult->fetch_assoc()) { $parties[] = $row; }

    $positionsResult = $conn->query("SELECT position_id, position_name FROM Positions WHERE election_id = '$election_id' ORDER BY position_id ASC");
    $positions = array();
    while ($row = $positionsResult->fetch_assoc()) { $positions[] = $row; }

    $candidatesResult = $conn->query(
        "SELECT c.candidate_id, c.party_id, c.position_id, c.student_id, s.first_name, s.last_name
         FROM Candidates c
         JOIN Students s ON c.student_id = s.student_id
         WHERE c.election_id = '$election_id'
         ORDER BY s.last_name ASC"
    );
    $candidates = array();
    while ($row = $candidatesResult->fetch_assoc()) { $candidates[] = $row; }

    return array("parties" => $parties, "positions" => $positions, "candidates" => $candidates);
}

function getPartiesByElection($conn, $election_id) {
    $sql = "SELECT party_id, party_name FROM PoliticalParties WHERE election_id = '$election_id' AND status = 'active' ORDER BY party_name ASC";
    $result = $conn->query($sql);
    $parties = array();
    while ($row = $result->fetch_assoc()) { $parties[] = $row; }
    return $parties;
}

function searchStudents($conn, $search_term, $election_id) {
    $sql = "SELECT s.student_id, s.first_name, s.last_name
            FROM Students s
            WHERE (s.first_name LIKE '%$search_term%' OR s.middle_name LIKE '%$search_term%'
                OR s.last_name LIKE '%$search_term%' OR s.student_id LIKE '%$search_term%')
            AND s.student_id NOT IN (SELECT student_id FROM Candidates WHERE election_id = '$election_id')
            LIMIT 10";
    $result = $conn->query($sql);
    $students = array();
    while ($row = $result->fetch_assoc()) { $students[] = $row; }
    return $students;
}

function addCandidate($conn, $student_id, $position_id, $party_id, $election_id) {
    $sql = "INSERT INTO Candidates (party_id, student_id, election_id, position_id) VALUES ('$party_id', '$student_id', '$election_id', '$position_id')";
    if ($conn->query($sql)) {
        return array("success" => true, "message" => "Candidate added successfully.");
    } else {
        return array("success" => false, "message" => "Failed to add candidate.");
    }
}

function removeCandidate($conn, $candidate_id) {
    $check = $conn->query("SELECT vote_id FROM Votes WHERE candidate_id = '$candidate_id' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        return array("success" => false, "message" => "Cannot remove. This candidate already has votes.");
    }
    if ($conn->query("DELETE FROM Candidates WHERE candidate_id = '$candidate_id'")) {
        return array("success" => true, "message" => "Candidate removed successfully.");
    } else {
        return array("success" => false, "message" => "Failed to remove candidate.");
    }
}
?>