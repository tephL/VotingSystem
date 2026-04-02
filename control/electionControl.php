<?php
include("../model/electionModel.php");

$action = $_GET['action'];

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
        getPositionsByElection($_GET['id']);
        break;
    default:
        echo "error";
        break;
}
?>
