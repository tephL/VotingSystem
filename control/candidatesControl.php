<?php
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
    echo json_encode([
        "success" => true,
        "data" => getElections($conn)
    ]);
    break;

    case "get_positions":
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        echo json_encode([
            "success" => true,
            "data" => getPositionsByElection($conn, $election_id)
        ]);
        break;

    case "get_parties":
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        echo json_encode([
            "success" => true,
            "data" => getPartiesByElection($conn, $election_id)
        ]);
        break;

    case "get_candidates_by_party_position":
        $party_id    = isset($_GET['party_id']) ? $_GET['party_id'] : '';
        $position_id = isset($_GET['position_id']) ? $_GET['position_id'] : '';

        echo json_encode([
            "success" => true,
            "data" => getCandidatesByPartyAndPosition($conn, $party_id, $position_id)
        ]);
        break;

    case "get_slate":
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';
        echo json_encode([
            "success" => true,
            "data" => getSlate($conn, $election_id)
        ]);
        break;

    case "search_students":
        $search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
        $election_id = isset($_GET['election_id']) ? $_GET['election_id'] : '';

        echo json_encode([
            "success" => true,
            "data" => searchStudents($conn, $search_term, $election_id)
        ]);
        break;

    case "add_candidate":

        if (empty($_POST['election_id'])) {
            echo json_encode(["success" => false, "message" => "Select election first."]);
            exit;
        }
        if (empty($_POST['party_id'])) {
            echo json_encode(["success" => false, "message" => "Select party."]);
            exit;
        }
        if (empty($_POST['position_id'])) {
            echo json_encode(["success" => false, "message" => "Select position."]);
            exit;
        }
        if (empty($_POST['student_id'])) {
            echo json_encode(["success" => false, "message" => "Select student."]);
            exit;
        }

        echo json_encode(
            addCandidate(
                $conn,
                $_POST['student_id'],
                $_POST['position_id'],
                $_POST['party_id'],
                $_POST['election_id']
            )
        );
        break;

    case "remove_candidate":

        if (empty($_POST['candidate_id'])) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid candidate."
            ]);
            exit;
        }

        echo json_encode(removeCandidate($conn, $_POST['candidate_id']));
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Unknown action."
        ]);
        break;
}
?>
