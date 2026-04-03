<?php
// candidatesControl.php
header("Content-Type: application/json");

require_once __DIR__ . "/../Model/dbconn.php";
require_once __DIR__ . "/../Model/candidatesModel.php";

$action = '';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}

switch ($action) {

    case "get_elections":
        $elections = getElections($conn);
        echo json_encode(array("success" => true, "data" => $elections));
        break;

    case "get_positions":
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        $positions = getPositionsByElection($conn, $election_id);
        echo json_encode(array("success" => true, "data" => $positions));
        break;

    case "get_parties":
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        $parties = getPartiesByElection($conn, $election_id);
        echo json_encode(array("success" => true, "data" => $parties));
        break;

    case "get_candidates":
        $position_id = isset($_GET['position_id']) ? $_GET['position_id'] : '';
        $candidates = getCandidatesByPosition($conn, $position_id);
        echo json_encode(array("success" => true, "data" => $candidates));
        break;

    case "get_candidates_by_party_position":
        $party_id    = isset($_GET['party_id'])    ? $_GET['party_id']    : '';
        $position_id = isset($_GET['position_id']) ? $_GET['position_id'] : '';
        $candidates  = getCandidatesByPartyAndPosition($conn, $party_id, $position_id);
        echo json_encode(array("success" => true, "data" => $candidates));
        break;

    case "get_slate":
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        $slate = getSlate($conn, $election_id);
        echo json_encode(array("success" => true, "data" => $slate));
        break;

    case "search_students":
        $search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        $students = searchStudents($conn, $search_term, $election_id);
        echo json_encode(array("success" => true, "data" => $students));
        break;

    case "add_candidate":
        $student_id  = isset($_POST['student_id'])  ? $_POST['student_id']  : '';
        $position_id = isset($_POST['position_id']) ? $_POST['position_id'] : '';
        $party_id    = isset($_POST['party_id'])    ? $_POST['party_id']    : '';
        $election_id = isset($_POST['election_id']) ? $_POST['election_id'] : '';
        $result = addCandidate($conn, $student_id, $position_id, $party_id, $election_id);
        echo json_encode($result);
        break;

    case "remove_candidate":
        $candidate_id = isset($_POST['candidate_id']) ? $_POST['candidate_id'] : '';
        $result = removeCandidate($conn, $candidate_id);
        echo json_encode($result);
        break;

    default:
        echo json_encode(array("success" => false, "message" => "Unknown action."));
        break;
}
?>