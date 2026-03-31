<?php
include("../model/electionModel.php");

$action = $_GET['action'];

if($action == "create"){
    createElections();
}
else if($action == "getAll"){
    getElection();
}
else if($action == "getById"){
    getElectionById();
}
else if($action == "update"){
    updateElection();
}
else if($action == "delete"){
    deleteElection();
}
else{
    echo "error";
}
?>
