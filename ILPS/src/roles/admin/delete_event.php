<?php
include '../../../config/db.php';
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();

$accId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;

    if ($event_id) {
        $delete_query = "CALL sp_delEventSched(?)";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $event_id);
        
        if ($stmt->execute()) {
            // Insert action in the logs
            $action = "Removed scheduled event (id: $event_id).";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Event ID not provided.']);
    }
}

?>
