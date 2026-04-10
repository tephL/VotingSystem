<?php


function instantiateDbForUser($username, $password){
    $conn = new mysqli("localhost", "$username", "$password", "VotingSystem");

    if($conn->connect_error){
        echo "error";
        return null;
    }else{
        return $conn;
    }
}

?>
