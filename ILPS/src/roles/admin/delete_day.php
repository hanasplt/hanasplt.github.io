<?php
    include 'db.php'; 

    if (isset($_POST['day_id'])) {
        $day_id = $_POST['day_id'];

        $query = "DELETE FROM scheduled_days WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $day_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting the schedule.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }

    $conn->close();
?>
