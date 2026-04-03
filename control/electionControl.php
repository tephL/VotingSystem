<?php
// electionControl.php

require_once __DIR__ . "/../model/electionModel.php";

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {

    case "create":
        createElections();
        break;

    case "getAll":
        getElection();
        break;

    case "getById":
        getElectionById();
        break;

    case "update":
        updateElection();
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
        echo json_encode(array("success" => false, "message" => "Unknown action."));
        break;
}
?>