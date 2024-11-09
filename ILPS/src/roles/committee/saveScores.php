<?php

require_once '../../../config/sessionConfig.php';
require_once '../../../config/db.php';
require_once '../admin/verifyLoginSession.php';
require_once 'committeePermissions.php';

$accId = $_SESSION['userId'];


// Function to insert or update sub_results
function insertOrUpdateSubResult($conn, $evId, $contestantId, $accId, $totalScore)
{
    // Check if the sub_result already exists
    $checkQuery = "SELECT * FROM sub_results WHERE eventId = ? AND contestantId = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $evId, $contestantId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows < 1) {
        // Insert new sub_result
        $insertQuery = "INSERT INTO sub_results (eventId, contestantId, personnelId, total_score) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iiid", $evId, $contestantId, $accId, $totalScore);
        $success = $insertStmt->execute();
        $insertStmt->close();

        if ($success) {
            $evname = isset($_GET['name']) ? $_GET['name'] : '';
            // Update the status of the scheduled event to 'Ended'
            $updateStatusQuery = "UPDATE scheduled_eventstoday SET status = 'Ended' WHERE activity = ?";
            $updateStatusStmt = $conn->prepare($updateStatusQuery);
            $updateStatusStmt->bind_param('s', $evname);
            $updateSuccess = $updateStatusStmt->execute();
            $updateStatusStmt->close();

            // Fetch team names with their contId
            $query_teams = "SELECT vt.teamId, vt.teamName, ve.contId FROM vw_eventparti ve INNER JOIN vw_teams vt ON ve.teamId = vt.teamId;";
            $result_teams = $conn->query($query_teams);

            $teams = [];
            if ($result_teams->num_rows > 0) {
                while ($row_team = $result_teams->fetch_assoc()) {
                    $teams[$row_team['contId']] = $row_team['teamName']; // Use contId as the key
                }
            }

            // Initialize a specific teamName by contId
            $contId = $contestantId; // Assuming $contestantId is defined and holds the desired contId
            $teamName = $teams[$contId] ?? 'Unknown'; // 'Unknown' if contId not found

            // Insert in the logs
            $action = "Scores Inserted for Team $teamName in the $evname.";
            $insertLogAct = "CALL sp_insertLog(?, ?)";
            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();
            $stmt->close();


            return [
                'success' => true,
                'message' => "Score for contestant ID $contestantId has been inserted successfully.",
                'eventStatusUpdated' => $updateSuccess
            ];
        } else {
            return ['success' => false, 'message' => "Error inserting score for contestant ID $contestantId."];
        }
    } else {
        $evname = isset($_GET['name']) ? $_GET['name'] : '';
        // Update existing sub_result
        $updateQuery = "UPDATE sub_results SET total_score = ? WHERE eventId = ? AND contestantId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("dii", $totalScore, $evId, $contestantId);
        $success = $updateStmt->execute();
        $updateStmt->close();


        // Fetch team names with their contId
        $query_teams = "SELECT vt.teamId, vt.teamName, ve.contId FROM vw_eventparti ve INNER JOIN vw_teams vt ON ve.teamId = vt.teamId;";
        $result_teams = $conn->query($query_teams);

        $teams = [];
        if ($result_teams->num_rows > 0) {
            while ($row_team = $result_teams->fetch_assoc()) {
                $teams[$row_team['contId']] = $row_team['teamName']; // Use contId as the key
            }
        }

        // Initialize a specific teamName by contId
        $contId = $contestantId; // Assuming $contestantId is defined and holds the desired contId
        $teamName = $teams[$contId] ?? 'Unknown'; // 'Unknown' if contId not found


        // Insert in the logs
        $action = "Scores Updated for Team $teamName in the $evname.";
        $insertLogAct = "CALL sp_insertLog(?, ?)";
        $stmt = $conn->prepare($insertLogAct);
        $stmt->bind_param("is", $accId, $action);
        $stmt->execute();
        $stmt->close();

        return [
            'success' => $success,
            'message' => $success ? "Score for contestant ID $contestantId has been updated successfully." : "Error updating score for contestant ID $contestantId."
        ];
    }

    $checkStmt->close();
}

// Decode JSON data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    // Extract data
    $eventId = $data['eventId'];
    $scores = $data['scores'];

    $responses = [];

    // Process each score
    foreach ($scores as $scoreData) {
        $contestantId = $scoreData['contestantId'];
        $score = $scoreData['score'];

        // Check if contestant ID exists
        $contestantCheckQuery = "SELECT contId FROM contestant WHERE contId = ?";
        $contestantCheckStmt = $conn->prepare($contestantCheckQuery);
        $contestantCheckStmt->bind_param("i", $contestantId);
        $contestantCheckStmt->execute();
        $contestantCheckResult = $contestantCheckStmt->get_result();

        if ($contestantCheckResult->num_rows < 1) {
            $responses[] = ['success' => false, 'message' => "Contestant ID $contestantId does not exist."];
            continue;
        }

        // Insert or update sub_result
        $responses[] = insertOrUpdateSubResult($conn, $eventId, $contestantId, $accId, $score);
    }

    // Return JSON-encoded response to JavaScript
    echo json_encode($responses);
} else {
    echo json_encode(['success' => false, 'message' => "No valid data received."]);
}
