<?php

require_once '../../../config/sessionConfig.php';
$conn = require_once '../../../config/db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['evID']; // Event ID
    $eventname = $_POST['evname']; // Event Name
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
        
        // Insert in the logs
        $action = "Scores have been added to teams under the $eventname event.";
        $insertLogAct = "CALL sp_insertLog(?, ?)";

        $stmt = $conn->prepare($insertLogAct);
        $stmt->bind_param("is", $id, $action);
        $stmt->execute();

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