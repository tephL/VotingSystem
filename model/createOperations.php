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

    function isElectionOngoing($end_date) {
        $current_time = new DateTime('now');
        $election_end = new DateTime($end_date);
        return $current_time < $election_end;
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
            calculateVotePercentage($candidates);
            $positions_data[$position_name] = $candidates;
        }
        
        return ['success' => true, 'data' => $positions_data];
    }
?>