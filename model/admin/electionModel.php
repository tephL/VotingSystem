<?php

date_default_timezone_set('Asia/Manila');

include __DIR__ . "./../dbconn.php";

$conn = instantiateDbForUser("root", "");

function getStatus($start, $end) {
    $now   = date('Y-m-d H:i:s');
    $start = date('Y-m-d H:i:s', strtotime($start));
    $end   = date('Y-m-d H:i:s', strtotime($end));

    if ($now < $start) {
        return "Upcoming";
    } else if ($now >= $end) { 
        return "Completed";
    } else {
        return "Active";
    }
}

function createElections() {
    global $conn;

    $title = $_POST['title'];
    $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
    $end   = date('Y-m-d H:i:s', strtotime($_POST['end']));
    
    $positions = array();
    if (isset($_POST['positions'])) {
        $positions = $_POST['positions'];
    }
    
    $parties = array();
    if (isset($_POST['parties'])) {
        $parties = $_POST['parties'];
    }

    if (count($parties) < 2) {
        echo "min_parties";
        return;
    }

    $now = date('Y-m-d H:i:s');
    
    if ($start < $now) {
        echo "past";
        return;
    }

    if ($start > $end) {
        echo "invalid";
        return;
    }

    $status = getStatus($start, $end);

    if ($status === "Active") {
        $check = $conn->query("SELECT * FROM Elections WHERE start_date <= '$now' AND end_date >= '$now'");
        if ($check->num_rows > 0) {
            echo "active";
            return;
        }
    }

    $sql = "INSERT INTO Elections (election_title, status, start_date, end_date)
            VALUES ('$title', '$status', '$start', '$end')";

    if ($conn->query($sql) === TRUE) {
        $election_id = $conn->insert_id;

        foreach ($positions as $p) {
            $posName = $p['name'];
            $posMax  = $p['max'];
            $conn->query("INSERT INTO Positions (position_name, max_votes, election_id) 
                          VALUES ('$posName', $posMax, $election_id)");
        }

        foreach ($parties as $party) {
            $partyName = $party['name'];
            $conn->query("INSERT INTO PoliticalParties (party_name, election_id) 
                          VALUES ('$partyName', $election_id)");
        }

        echo "success";
    } else {
        echo "error";
    }
}

function updateElection() {
    global $conn;

    $id    = $_POST['id'];
    $title = $_POST['title'];
    $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
    $end   = date('Y-m-d H:i:s', strtotime($_POST['end']));
    
    $positions = array();
    if (isset($_POST['positions'])) {
        $positions = $_POST['positions'];
    }
    
    $parties = array();
    if (isset($_POST['parties'])) {
        $parties = $_POST['parties'];
    }

    if (count($parties) < 2) {
        echo "min_parties";
        return;
    }

    if ($start > $end) {
        echo "invalid";
        return;
    }

    $status = getStatus($start, $end);

    if ($status === "Active") {
        $now = date('Y-m-d H:i:s');
        $check = $conn->query("SELECT * FROM Elections 
                               WHERE start_date <= '$now' 
                               AND end_date >= '$now' 
                               AND election_id != $id");
        if ($check->num_rows > 0) {
            echo "active";
            return;
        }
    }

    $sql = "UPDATE Elections SET 
            election_title = '$title',
            status         = '$status',
            start_date     = '$start',
            end_date       = '$end'
            WHERE election_id = $id";

    if ($conn->query($sql) === TRUE) {

        $incomingPositionIds = array();

        foreach ($positions as $p) {
            $posName = $p['name'];
            $posMax  = $p['max'];
            $posId   = "";

            if (isset($p['position_id'])) {
                $posId = $p['position_id'];
            }

            if ($posId != "") {
                $conn->query("UPDATE Positions SET position_name = '$posName', max_votes = $posMax 
                              WHERE position_id = $posId AND election_id = $id");
                $incomingPositionIds[] = $posId;
            } else {
                $conn->query("INSERT INTO Positions (position_name, max_votes, election_id)
                              VALUES ('$posName', $posMax, $id)");
                $incomingPositionIds[] = $conn->insert_id;
            }
        }

        if (count($incomingPositionIds) > 0) {
            $sql_delete_pos = "DELETE FROM Positions WHERE election_id = $id AND position_id NOT IN (";
            $i = 0;
            foreach ($incomingPositionIds as $pid) {
                if ($i > 0) {
                    $sql_delete_pos = $sql_delete_pos . ",";
                }
                $sql_delete_pos = $sql_delete_pos . $pid;
                $i = $i + 1;
            }
            $sql_delete_pos = $sql_delete_pos . ")";
            $conn->query($sql_delete_pos);
        } else {
            $conn->query("DELETE FROM Positions WHERE election_id = $id");
        }

        if (count($incomingPositionIds) > 0) {
            $sql_delete_cand = "DELETE FROM Candidates WHERE election_id = $id AND position_id NOT IN (";
            $i = 0;
            foreach ($incomingPositionIds as $pid) {
                if ($i > 0) {
                    $sql_delete_cand = $sql_delete_cand . ",";
                }
                $sql_delete_cand = $sql_delete_cand . $pid;
                $i = $i + 1;
            }
            $sql_delete_cand = $sql_delete_cand . ")";
            $conn->query($sql_delete_cand);
        } else {
            $conn->query("DELETE FROM Candidates WHERE election_id = $id");
        }

        foreach ($parties as $party) {
            $partyName = $party['name'];
            $partyId   = "";

            if (isset($party['party_id'])) {
                $partyId = $party['party_id'];
            }

            if ($partyId == "") {
                $conn->query("INSERT INTO PoliticalParties (party_name, election_id) VALUES ('$partyName', $id)");
            } else {
                $conn->query("UPDATE PoliticalParties SET party_name = '$partyName' WHERE party_id = $partyId");
            }
        }

        if ($status === "Completed") {
            $conn->query("UPDATE PoliticalParties SET status = 'inactive' WHERE election_id = $id");
        }

        echo "success";
    } else {
        echo "error";
    }
}

function getElection() {
    global $conn;

    $result = $conn->query("SELECT * FROM Elections ORDER BY election_id DESC");
    $data   = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $newStatus = getStatus($row['start_date'], $row['end_date']);

            if ($row['status'] != $newStatus) {
                $id = $row['election_id'];
                $conn->query("UPDATE Elections SET status = '$newStatus' WHERE election_id = $id");
                
                if ($newStatus === "Completed") {
                    $conn->query("UPDATE PoliticalParties SET status = 'inactive' WHERE election_id = $id");
                }
            }

            $row['status'] = $newStatus;
            $data[] = $row;
        }
    }

    echo json_encode($data);
}

function getElectionById() {
    global $conn;

    $id     = $_GET['id'];
    $result = $conn->query("SELECT * FROM Elections WHERE election_id = $id");

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo "error";
    }
}

function deleteElection() {
    global $conn;

    $id = $_POST['id'];   

    if ($id <= 0) {
        echo "invalid";
        return;
    }

    $conn->begin_transaction();

    try {
        $conn->query("DELETE FROM Votes WHERE election_id = $id");
        $conn->query("DELETE FROM Candidates WHERE election_id = $id");
        $conn->query("DELETE FROM PoliticalParties WHERE election_id = $id");
        $conn->query("DELETE FROM Positions WHERE election_id = $id");
        $conn->query("DELETE FROM Elections WHERE election_id = $id");

        $conn->commit();
        echo "success";

    } catch (Exception $e) {
        $conn->rollback();
        echo "error";
    }
}

function getPositionsByElection($election_id) {
    global $conn;
    
    $sql = "SELECT position_id, position_name, max_votes 
            FROM Positions 
            WHERE election_id = $election_id 
            ORDER BY position_id ASC";
    
    $result    = $conn->query($sql);
    $positions = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $positions[] = $row;
        }
    }
    
    echo json_encode($positions);
}

function getPartiesByElection($election_id) {
    global $conn;

    $sql = "SELECT party_id, party_name
            FROM PoliticalParties
            WHERE election_id = $election_id
            ORDER BY party_name ASC";

    $result  = $conn->query($sql);
    $parties = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $parties[] = $row;
        }
    }

    echo json_encode($parties);
}

function removeParty() {
    global $conn;

    $party_id = $_POST['party_id'];

    $conn->query("DELETE FROM Votes WHERE candidate_id IN 
                  (SELECT candidate_id FROM Candidates WHERE party_id = $party_id)");
    $conn->query("DELETE FROM Candidates WHERE party_id = $party_id");
    $conn->query("DELETE FROM PoliticalParties WHERE party_id = $party_id");

    echo "success";
}
?>