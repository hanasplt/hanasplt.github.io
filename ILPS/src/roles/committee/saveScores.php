<?php

require_once '../../../config/sessionConfig.php';
require_once '../../../config/db.php';
require_once '../admin/verifyLoginSession.php';
require_once 'committeePermissions.php';

// Function to insert or update sub_results
function insertOrUpdateSubResult($conn, $evId, $contestantId, $personnelId, $totalScore) {
    $checkQuery = "SELECT * FROM sub_results WHERE eventId = ? AND contestantId = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $evId, $contestantId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows < 1) {
        $insertQuery = "INSERT INTO sub_results (eventId, contestantId, personnelId, total_score) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iiid", $evId, $contestantId, $personnelId, $totalScore);
        $success = $insertStmt->execute();
        $insertStmt->close();
        
        return $success ?
            "Score for contestant ID $contestantId has been inserted successfully." :
            "Error inserting score for contestant ID $contestantId.";
    } else {
        $updateQuery = "UPDATE sub_results SET total_score = ? WHERE eventId = ? AND contestantId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("dii", $totalScore, $evId, $contestantId);
        $success = $updateStmt->execute();
        $updateStmt->close();

        return $success ?
            "Score for contestant ID $contestantId has been updated successfully." :
            "Error updating score for contestant ID $contestantId.";
    }

    $checkStmt->close();
}

// Decode JSON data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    // Extract data
    $eventId = $data['eventId'];
    $scores = $data['scores'];
    $personnelId = $_SESSION['userId'];

    $responses = [];

    // Process each score
    foreach ($scores as $scoreData) {
        $contestantId = $scoreData['teamId'];
        $score = $scoreData['score'];

        // Check if contestant ID exists
        $contestantCheckQuery = "SELECT teamId FROM contestant WHERE teamId = ?";
        $contestantCheckStmt = $conn->prepare($contestantCheckQuery);
        $contestantCheckStmt->bind_param("i", $contestantId);
        $contestantCheckStmt->execute();
        $contestantCheckResult = $contestantCheckStmt->get_result();

        if ($contestantCheckResult->num_rows < 1) {
            $responses[] = ['success' => false, 'message' => "Contestant ID $contestantId does not exist."];
            continue;
        }

        // Insert or update sub_result
        $responses[] = insertOrUpdateSubResult($conn, $eventId, $contestantId, $personnelId, $score);
    }

    // Return JSON-encoded response to JavaScript
    echo json_encode($responses);
} else {
    echo json_encode(['success' => false, 'message' => "No valid data received."]);
}

?>