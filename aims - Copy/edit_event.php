<?php
include 'db.php';

if (isset($_POST['event_id'], $_POST['time'], $_POST['activity'], $_POST['location'], $_POST['status'])) {
    $event_id = $_POST['event_id'];
    $time = $_POST['time'];
    $activity = ucwords($_POST['activity']);
    $location = $_POST['location'];
    $status = $_POST['status'];

    $query = "UPDATE scheduled_eventstoday SET time = ?, activity = ?, location = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssi', $time, $activity, $location, $status, $event_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update event']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
