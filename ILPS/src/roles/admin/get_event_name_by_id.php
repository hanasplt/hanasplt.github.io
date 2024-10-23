<?php

include '../../../config/db.php';

if (isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    $sql = "SELECT activity FROM scheduled_eventstoday WHERE id = ? AND type = 'Others'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $stmt->bind_result($eventName);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'eventName' => $eventName]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No event ID provided']);
}
?>
