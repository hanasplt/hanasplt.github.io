<?php
include '../../../config/db.php';
session_start();

$accId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dayId = $_POST['day_id'];
    $dayDate = $_POST['day_date'];

    $query = "CALL sp_updateDay(?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $dayDate, $dayId);

    if ($stmt->execute()) {
        // Insert action in the logs
        $action = "Updated day(id: $dayId) to ($dayDate) in the Schedule.";
        $insertLogAct = "CALL sp_insertLog(?, ?)";

        $stmt = $conn->prepare($insertLogAct);
        $stmt->bind_param("is", $accId, $action);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update date']);
    }
}
?>
