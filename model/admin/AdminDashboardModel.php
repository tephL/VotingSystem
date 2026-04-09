 <?php
 
    include_once(__DIR__ . "/./readOperations.php");
    include_once(__DIR__ . "/../dbconn.php");

    $conn = instantiateDbForUser("root", "");
 
    function getCurrentElection() {
        global $conn;
        $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections WHERE status = 'active' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            // No active election, get the most recently completed one
            $sql = "SELECT election_id, election_title, status, start_date, end_date FROM Elections WHERE status = 'completed' ORDER BY end_date DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            
            if (!$result || mysqli_num_rows($result) == 0) {
                return null;
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

?>
