<?php

include '../../../config/db.php';
session_start();

$accId = $_SESSION['userId'];
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

if (!isset($_POST['day_date'])) {
    die(json_encode(['success' => false, 'message' => 'Day date is not provided.']));
}

$day_date = $_POST['day_date'];

$sql = "CALL sp_insertDay(?)";
$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $day_date);

if ($stmt->execute()) {
    // Insert action in the logs
    $action = "Added date ($day_date) in the Schedule.";
    $insertLogAct = "CALL sp_insertLog(?, ?)";

    $stmt = $conn->prepare($insertLogAct);
    $stmt->bind_param("is", $accId, $action);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding new day: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
