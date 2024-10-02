<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day_id = $_POST['day_id'];
    $time = $_POST['time'];
    $activity = ucwords($_POST['activity']);
    $location = $_POST['location'];
    $status = $_POST['status'];

    if (empty($day_id) || empty($time) || empty($activity) || empty($location) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $query = "INSERT INTO scheduled_eventstoday (day_id, time, activity, location, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $day_id, $time, $activity, $location, $status);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add event.']);
    }

    $stmt->close();
    $conn->close();
}
?>
