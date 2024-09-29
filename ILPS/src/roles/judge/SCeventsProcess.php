<?php

require_once '../../../config/sessionConfig.php';
$conn = require_once '../../../config/db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['userId'];

/* 
    Record Score Sheet of the Judge (TBC)

    Per contestant, criteria score and total score will be in an array.
    Doing so, enables insertion of score per contestant in the database.


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'record') {
    $event = $_POST['evID']; // Event ID
    $total = $_POST['totalScore']; // So is the totalScore
    $parti = $_POST['contestant']; // Another pending, should be the same as criteria is inserted
    $criteria_scores = $_POST['criteria'];

    $criteria_values = array_fill(0, 10, 0);

    $index = 0;
    foreach ($criteria_scores as $criteria => $score) {
        if ($index < 10) {
            $criteria_values[$index] = $score;
            $index++;
        }
    }

    $sql = "CALL sp_insertResult(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $params = array_merge([$event, $parti, $id, $total], $criteria_values);
    $types = "iisd" . str_repeat("d", count($criteria_values));
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo '<script>alert("Recorded!");</script>';

        // No longer needed because judge can score all teams within that event
        /*
        $sql = "CALL sp_updateContStat(?)"; // Better change this to
        $prep = $conn->prepare($sql);
        $prep->bind_param("i", $parti);
        $result = $prep->execute();

        if (!$result) {
            echo '<script>alert("Failed to update status!");</script>';
            error_log("Error updating status: " . $conn->error);
        }
    } else {
        echo '<script>alert("Failed to record!");</script>';
    }
    $stmt->close();
}
    */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['evID']; // Event ID
    $contestants = $_POST['contestant']; // Contestant IDs
    $criteria_scores = $_POST['criteria']; // Criteria scores for each contestant

    try { // Exception handling for inserting score
        // Loop through each contestant to process their scores
        foreach ($contestants as $contestant) {
            $teamCount = $contestant; // Use contestant ID as teamCount

            // Retrieve the total score for this contestant
            $totalScore = $_POST['totalScore' . $teamCount];
            
            // Retrieve criteria scores for this contestant
            $criteria_values = array_fill(0, 10, 0); // Array to hold criteria values

            $index = 0; 

            if (isset($criteria_scores[$contestant])) {
                foreach ($criteria_scores[$contestant] as $criteriaId => $score) {
                    if ($index < 10) { // Number of criteria that can be inserted in the database
                        $criteria_values[$index] = $score;
                        $index++; // Increment index
                    }
                }
            }

            $sql = "CALL sp_insertResult(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Merge parameters (eventId, contestantId, personnelId, totalScore, and criteria scores)
            $params = array_merge([$eventId, $contestant, $id, $totalScore], $criteria_values);
            $types = "iiid" . str_repeat("d", count($criteria_values));

            $stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Error inserting results for contestant: '. $stmt->error
                ]);
                exit;
            }
        }
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Score Successfully Added!'
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error: '. $e->getMessage()
        ]);
        exit;
    }

}

$conn->close();
?>