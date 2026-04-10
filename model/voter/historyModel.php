<?php


include_once(__DIR__ . "/../dbconn.php");

$conn = instantiateDbForUser("root", "");

function getVotesOfAUser($user_id){
    global $conn;

    $sql = "SELECT 
                e.election_id,
                e.election_title,
                p.position_id,
                p.position_name,
                pa.party_name,
                s.student_id,
                CONCAT(s.first_name, ' ', s.last_name) AS candidate_name,
                v.vote_date
            FROM Users u
            JOIN StudentVoters sv ON sv.user_id = u.user_id
            JOIN Votes v ON v.studentvoter_id = sv.studentvoter_id
            JOIN Elections e ON e.election_id = v.election_id
            JOIN Positions p ON p.position_id = v.position_id
            LEFT JOIN Candidates c ON c.candidate_id = v.candidate_id
            LEFT JOIN Students s ON s.student_id = c.student_id
            LEFT JOIN PoliticalParties pa ON pa.party_id = c.party_id
            WHERE u.user_id = $user_id
            ORDER BY e.election_id, p.position_id;";
    $r_sql = $conn->query($sql);
    return $r_sql;
}

?>