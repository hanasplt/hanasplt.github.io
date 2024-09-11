<?php
// Include your database connection
include 'db_connect.php'; // Adjust this path as necessary

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data
    $day_number = isset($_POST['day_number']) ? intval($_POST['day_number']) : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $activity = isset($_POST['activity']) ? $_POST['activity'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : 'Pending';

    // Check if all required fields are provided
    if ($day_number && $time && $activity && $location) {
        // Prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO scheduled_eventstoday (day_id, time, activity, location, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $day_number, $time, $activity, $location, $status);

        if ($stmt->execute()) {
            // Success response
            echo json_encode(['success' => true]);
        } else {
            // Error response in case of SQL failure
            echo json_encode(['success' => false, 'message' => 'Failed to add the event.']);
        }

        $stmt->close();
    } else {
        // Error response if some data is missing
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    }
} else {
    // Error response for invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>

