<?php
include '../../../config/db.php';

if (isset($_POST['event_id'])) {
    $eventId = intval($_POST['event_id']);

    $sql = "CALL sp_getEventContestant(?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $teams[] = [
                'teamId' => $row['teamId'],  // Ensure 'teamId' matches the JavaScript
                'teamName' => htmlspecialchars($row['team'])  // Sanitize the team name
            ];
        }

        echo json_encode(['success' => true, 'teams' => $teams]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No teams found']);
    }

    $result->free();
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
