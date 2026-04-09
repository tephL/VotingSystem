<?php 


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


include_once(__DIR__ . "/./readOperations.php");
include_once(__DIR__ . "/../dbconn.php");

$conn = instantiateDbForUser("root", "");


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


?>
