<?php
include '../../../config/db.php';

session_start();

$accId = $_SESSION['userId'];

    if (isset($_POST['day_id'])) {
        $day_id = $_POST['day_id'];
        $day_date = $_POST['day_date'];

        $query = "CALL sp_delDay(?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $day_id);

        if ($stmt->execute()) {
            // Insert action in the logs
            $action = "Deleted date id: $day_id ($day_date) in the Schedule.";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

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
