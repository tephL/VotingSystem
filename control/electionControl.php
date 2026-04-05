<?php

require_once __DIR__ . "/../model/electionModel.php";

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

switch ($action) {
    
    case "create":

        if (
            empty($_POST['title']) ||
            empty($_POST['start']) ||
            empty($_POST['end'])
        ) {
            echo "invalid";
            exit;
        }

        createElections();
        break;
    
    case "update":

        if (
            empty($_POST['title']) ||
            empty($_POST['start']) ||
            empty($_POST['end'])
        ) {
            echo "invalid";
            exit;
        }

        updateElection();
        break;

    case "getAll":
        getElection();
        break;

    case "getById":
        getElectionById();
        break;

    case "delete":
        deleteElection();
        break;

    case "getPositions":
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        getPositionsByElection($id);
        break;

    case "getParties":
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        getPartiesByElection($id);
        break;

    case "removeParty":
        removeParty();
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Unknown action."
        ]);
        break;
}
?>
