<?php 
    include_once "../model/readOperations.php";

    function registerStudent($studentid, $username, $email, $password) {
    global $conn;

    $sql1 = "INSERT INTO Users (username, email, password, role_id) 
             VALUES ('$username', '$email', '$password', 1001)";
    $result = $conn->query($sql1);

    if ($result) {
        $currentUser = getUserID($username);

        $sql2 = "INSERT INTO StudentVoters (user_id, student_id) 
                 VALUES ('$currentUser', '$studentid')";
        $result = $conn->query($sql2);
    }

    return $result;
}


// =================== Election Results Aggregation/Calculation ===========================================================================

    function isElectionOngoing($status) {
        return $status === 'active';
    }

    function calculateVotePercentage(&$candidates) {
        $total_votes = 0;
        foreach ($candidates as $candidate) {
            $total_votes += intval($candidate['vote_count']);
        }
        foreach ($candidates as &$candidate) {
            if ($total_votes > 0) {
                $candidate['percentage'] = round((intval($candidate['vote_count']) / $total_votes) * 100, 1);
            } else {
                $candidate['percentage'] = 0;
            }
        }
    }

    function getElectionResults($election_id) {
        $eid = intval($election_id);
        try {
            $positions = getPositions($eid);
            if ($positions === null) {
                return ['success' => false, 'message' => 'No positions found', 'data' => []];
            }
            $positions_data = [];
            foreach ($positions as $position) {
                $position_id = intval($position['position_id']);
                $position_name = $position['position_name'];
                $candidates = getCandidates($position_id, $eid);
                if ($candidates === null) {
                    return ['success' => false, 'message' => 'Error retrieving candidates', 'data' => []];
                }
                calculateVotePercentage($candidates);
                $positions_data[] = [
                    'position_id' => $position_id,
                    'position_name' => $position_name,
                    'candidate_count' => count($candidates),
                    'candidates' => $candidates
                ];
            }
            return ['success' => true, 'message' => '', 'data' => $positions_data];
        } catch (Exception $e) {
            error_log('getElectionResults error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error: ' . $e->getMessage(), 'data' => []];
        }
    }
?>