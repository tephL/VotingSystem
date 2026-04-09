<?php

// will be removed
    // $conn = new mysqli("127.0.0.1", "root", "1234", "VotingSystem");

    // if($conn->connect_error){
    //     echo "error";
    // }


function instantiateDbForUser($username, $password){
    $conn = new mysqli("127.0.0.1", "$username", "$password", "VotingSystem");

    if($conn->connect_error){
        echo "error";
    }else{
        return $conn;
    }
}

?>
