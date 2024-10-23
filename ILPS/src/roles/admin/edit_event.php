<?php

include '../../../config/db.php';

if (isset($_POST['event_id'], $_POST['time'], $_POST['activity'], $_POST['location'], $_POST['status'])) {
    $event_id = $_POST['event_id'];
    $time = $_POST['time'];
    $activity = ucwords(trim($_POST['activity']));
    $location = ucwords(trim($_POST['location']));
    $status = $_POST['status'];

    $conn->begin_transaction();

    try {
        $updateQuery = "UPDATE scheduled_eventstoday SET time = ?, activity = ?, location = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ssssi', $time, $activity, $location, $status, $event_id);

        if ($stmt->execute()) {
            $selectQuery = "SELECT time, type, activity, gameNo, teamA, teamB, location, status FROM scheduled_eventstoday WHERE id = ?";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param('i', $event_id);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            if ($result->num_rows > 0) {
                $eventData = $result->fetch_assoc();
                $conn->commit();

                echo json_encode([
                    'success' => true,
                    'time' => $eventData['time'],
                    'type' => $eventData['type'],
                    'activity' => $eventData['activity'],
                    'gameNo' => $eventData['gameNo'],
                    'teamA' => $eventData['teamA'],
                    'teamB' => $eventData['teamB'],
                    'location' => $eventData['location'],
                    'status' => $eventData['status']
                ]);
            } else {
                throw new Exception('Failed to retrieve updated event');
            }

            $selectStmt->close();
        } else {
            throw new Exception('Failed to update event');
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
