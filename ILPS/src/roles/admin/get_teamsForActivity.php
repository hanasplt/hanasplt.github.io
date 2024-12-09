<?php
header('Content-Type: application/json');
include '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$activityId = isset($_POST['activityId']) ? intval($_POST['activityId']) : null;

if (!$activityId) {
    echo json_encode(['success' => false, 'message' => 'Activity ID is required.']);
    exit;
}

try {
    error_log("Fetching teams for activity ID: " . $activityId);

    $stmt = $conn->prepare("SELECT t.teamId, t.teamName 
                             FROM teams t
                             JOIN contestant at ON t.teamId = at.teamId
                             WHERE at.eventId = ?");
    $stmt->bind_param("i", $activityId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $teams = [];
        while ($team = $result->fetch_assoc()) {
            $teams[] = $team;
        }

        echo json_encode(['success' => true, 'teams' => $teams]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No teams found for the specified activity ID.']);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching teams: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching teams: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>