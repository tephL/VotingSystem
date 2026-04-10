<?php 
    include_once "../model/readOperations.php";
    include_once(__DIR__ . "/admin/readOperations.php");
    include_once(__DIR__ . "/dbconn.php");

    $conn = instantiateDbForUser("root", "");

    function isElectionOngoing($status) {
        return $status === 'active';
    }

    function getCurrentElection() {
        global $conn;
        $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections WHERE status = 'active' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            // No active election, get the most recently completed one
            $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections WHERE status = 'completed' ORDER BY end_date DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            
            if (!$result || mysqli_num_rows($result) == 0) {
                // No completed election, get the most recent upcoming one
                $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections WHERE status = 'upcoming' ORDER BY start_date ASC LIMIT 1";
                $result = mysqli_query($conn, $sql);
                
                if (!$result || mysqli_num_rows($result) == 0) {
                    return null;
                }
            }
        }
        
        return mysqli_fetch_assoc($result);
    }

    function getPositions($election_id) {
        global $conn;
        $eid = intval($election_id);
        $sql = "SELECT position_id, position_name, max_votes FROM Positions WHERE election_id = $eid ORDER BY position_id ASC";
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

    function getCandidates($position_id, $election_id) {
        global $conn;
        $pid = intval($position_id);
        $eid = intval($election_id);
        
        $sql = "
            SELECT 
                c.candidate_id,
                c.party_id,
                pp.party_name,
                CONCAT(s.first_name, ' ', IFNULL(CONCAT(s.middle_name, ' '), ''), s.last_name) AS candidate_name,
                COUNT(v.vote_id) AS vote_count
            FROM Candidates c
            JOIN Students s ON c.student_id = s.student_id
            JOIN PoliticalParties pp ON c.party_id = pp.party_id
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
        
        return $candidates;
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

    function getElectionCandidatesByParty($election_id) {
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
                $max_votes = intval($position['max_votes']);
                
                global $conn;
                $sql = "
                    SELECT 
                        pp.party_id,
                        pp.party_name,
                        c.candidate_id,
                        s.first_name,
                        s.middle_name,
                        s.last_name
                    FROM PoliticalParties pp
                    LEFT JOIN Candidates c ON pp.party_id = c.party_id AND c.position_id = $position_id AND c.election_id = $eid
                    LEFT JOIN Students s ON c.student_id = s.student_id
                    ORDER BY pp.party_id ASC, s.last_name ASC
                ";
                
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    return ['success' => false, 'message' => 'Error retrieving candidates', 'data' => []];
                }
                
                $parties = [];
                $current_party_id = null;
                while ($row = mysqli_fetch_assoc($result)) {
                    $party_id = $row['party_id'];
                    if ($current_party_id !== $party_id) {
                        $parties[] = [
                            'party_name' => $row['party_name'],
                            'candidates' => []
                        ];
                        $current_party_id = $party_id;
                    }
                    if ($row['candidate_id'] !== null) {
                        $parties[count($parties)-1]['candidates'][] = [
                            'first_name' => $row['first_name'],
                            'middle_name' => $row['middle_name'],
                            'last_name' => $row['last_name']
                        ];
                    }
                }
                
                $positions_data[] = [
                    'position_id' => $position_id,
                    'position_name' => $position_name,
                    'max_votes' => $max_votes,
                    'political_parties' => $parties
                ];
            }
            return ['success' => true, 'message' => '', 'data' => $positions_data];
        } catch (Exception $e) {
            error_log('getElectionCandidatesByParty error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error: ' . $e->getMessage(), 'data' => []];
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