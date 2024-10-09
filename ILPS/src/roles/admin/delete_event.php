<?php
include '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;

    if ($event_id) {
        $delete_query = "DELETE FROM scheduled_eventstoday WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $event_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Event ID not provided.']);
    }
}

?>
