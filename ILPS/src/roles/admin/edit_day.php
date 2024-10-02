<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dayId = $_POST['day_id'];
    $dayDate = $_POST['day_date'];

    $query = "UPDATE scheduled_days SET day_date = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $dayDate, $dayId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update date']);
    }
}
?>
