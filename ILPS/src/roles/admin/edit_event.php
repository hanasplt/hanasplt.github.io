<?php

include '../../../config/db.php';

if (isset($_POST['event_id'], $_POST['time'], $_POST['activity'], $_POST['location'], $_POST['status'])) {
    $event_id = $_POST['event_id'];
    $time = $_POST['time'];
    $activity = ucwords($_POST['activity']);
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Update the event in the database
    $query = "UPDATE scheduled_eventstoday SET time = ?, activity = ?, location = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssi', $time, $activity, $location, $status, $event_id);

    if ($stmt->execute()) {
        // After updating, retrieve the event data including teamA_name, teamB_name, type, etc.
        $selectQuery = "SELECT time, type, activity, gameNumber, teamA_name, teamB_name, location, status FROM scheduled_eventstoday WHERE id = ?";
        $selectStmt = $conn->prepare($selectQuery);
        $selectStmt->bind_param('i', $event_id);
        $selectStmt->execute();
        $result = $selectStmt->get_result();

        if ($result->num_rows > 0) {
            $eventData = $result->fetch_assoc();
            // Return the updated event data
            echo json_encode([
                'success' => true,
                'time' => $eventData['time'],
                'type' => $eventData['type'],
                'activity' => $eventData['activity'],
                'gameNumber' => $eventData['gameNumber'],
                'teamA_name' => $eventData['teamA_name'],
                'teamB_name' => $eventData['teamB_name'],
                'location' => $eventData['location'],
                'status' => $eventData['status']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to retrieve updated event']);
        }

        $selectStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update event']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}

?>
