<?php 
    include (__DIR__ . "./readOperations.php");

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
