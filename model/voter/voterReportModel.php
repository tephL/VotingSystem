<?php
    include_once(__DIR__ . "/../reportModel.php");

    function getVoterCurrentElection() {
        // Voters see upcoming or completed elections, excluding active
        try {
            $election = getCurrentElection();
            
            // Filter out active elections for voters
            if ($election && $election['status'] === 'active') {
                return null;
            }
            
            return $election;
        } catch (Exception $e) {
            error_log('getVoterCurrentElection error: ' . $e->getMessage());
            return null;
        }
    }

    function getVoterElectionResults($election_id, $election_status) {
        // Voters cannot see results for active elections
        if ($election_status === 'active') {
            return ['success' => false, 'message' => 'Election is currently active. Results unavailable.', 'data' => []];
        }

        if ($election_status === 'upcoming') {
            $results = getElectionCandidatesByParty($election_id);
        } else if ($election_status === 'completed') {
            $results = getElectionResults($election_id);
        } else {
            return ['success' => false, 'message' => 'Invalid election status', 'data' => []];
        }

        if (!$results["success"]) {
            return ['success' => false, 'message' => $results["message"], 'data' => []];
        }

        return ['success' => true, 'message' => '', 'data' => $results["data"]];
    }
?>
