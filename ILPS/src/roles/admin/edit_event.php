<?php
include '../../../config/db.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Required fields
    $event_id = $_POST['eventId'];
    $day_id = $_POST['dayId'];
    $time = $_POST['time'];
    $type = $_POST['type'];
    $activity = $_POST['activity'];
    $location = $_POST['location'];
    $status = $_POST['status'];

    // Set gameNo, teamA, and teamB based on the type
    if ($type === 'Sports') {
        $gameNo = !empty($_POST['gameNo']) ? (int)$_POST['gameNo'] : null;
        $teamA = isset($_POST['teamA']) ? (int)$_POST['teamA'] : null;
        $teamB = isset($_POST['teamB']) ? (int)$_POST['teamB'] : null;
    } else {
        // If the type is Socio-Cultural or Others, set to null
        $gameNo = null;
        $teamA = null;
        $teamB = null;
    }

    $missingFields = [];
    if (empty($event_id)) $missingFields[] = 'event_id';
    if (empty($day_id)) $missingFields[] = 'day_id';
    if (empty($time)) $missingFields[] = 'time';
    if (empty($type)) $missingFields[] = 'type';
    if (empty($activity)) $missingFields[] = 'activity';
    if (empty($location)) $missingFields[] = 'location';
    if (empty($status)) $missingFields[] = 'status';

    if (!empty($missingFields)) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing: ' . implode(', ', $missingFields)]);
        exit;
    }

    if (empty($event_id) || empty($day_id) || empty($time) || empty($type) || empty($activity) || empty($location) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Prepare update query
        $updateQuery = "UPDATE scheduled_eventstoday SET time = ?, type = ?, activity = ?, location = ?, gameNo = ?, teamA = ?, teamB = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);

        // Use appropriate types for bind_param
        // Since gameNo, teamA, and teamB can be NULL, bind them as nullable types
        $stmt->bind_param('ssssssss', $time, $type, $activity, $location, $gameNo, $teamA, $teamB, $status, $event_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event updated successfully.']);
        } else {
            throw new Exception('Failed to update event.');
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
}
?>
