<?php
header('Content-Type: application/json');
include "../model/readOperations.php";

$studentid = trim($_POST['studentid']);
$username  = trim($_POST['username']);
$password  = trim($_POST['password']);

// empty checks
if (empty($studentid)) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Username is required']);
    exit;
}
if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

// format checks
if (strlen($studentid) < 11 || !ctype_digit($studentid)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Student ID']);
    exit;
}
if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => 'Username too short']);
    exit;
}
if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password too short']);
    exit;
}
// duplication 
if (userExists($username)){
    echo json_encode(['success' => false, 'message' => 'Student with this username already exist']);
    exit;
}

$sql = "INSERT INTO Users (student_id, username, password, role_id) VALUES ('$studentid', '$username', '$password', 1001)";
$result = $conn->query($sql);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Registered successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}

?>