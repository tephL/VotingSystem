<?php
header('Content-Type: application/json');
include_once "../model/admin/readOperations.php";
include "../model/admin/createOperations.php";

$studentid = trim($_POST['studentid']);
$email = trim($_POST['email']);
$username  = trim($_POST['username']);
$password  = trim($_POST['password']);
$confirmpassword  = trim($_POST['confirmpassword']);

    function validateRegistration($studentid, $username, $email, $password, $confirmpassword){
        $errors = [];
        // empty checks
        if (empty($studentid) || empty($username) || empty($password)) {
            $errors[] = 'Please fill the required input';
        }
        // format checks
        if (strlen($studentid) !== 10) {
            $errors[] = 'Student ID must be exactly 10 digits';
        }
        if (!ctype_digit($studentid)){
            $errors[] = 'Student ID must contain numbers only';
        }
        if (strlen($username) < 5){
            $errors[] = 'Username too short';
        }
        if (!ctype_alnum($username)) {
            $errors[] = 'Username cannot contain spaces or special characters';
        }
        if (strlen($password) < 8){
            $errors[] = 'Password too short';
        }
        if ($confirmpassword != $password){
            $errors[] = 'Passwords do not match';
        }
        // if format checks failed return
        if (!empty($errors)) return $errors;

        // db check 
        if (userExists($username)){
            $errors[] = 'Username already taken';
        }

        return $errors;
    }

$errors = validateRegistration($studentid, $username, $email, $password, $confirmpassword);

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => $errors]);
    exit;
}

$result = registerStudent($studentid, $username, $email, $password);
if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Registered successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed'
    ]);
}

    
?>
