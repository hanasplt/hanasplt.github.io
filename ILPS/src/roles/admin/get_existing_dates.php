<?php
include '../../../config/db.php';

    if (isset($_POST['day_date'])) {
        $dayDate = $_POST['day_date'];

        $query = "SELECT COUNT(*) AS dateCount FROM scheduled_days WHERE day_date = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $dayDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['dateCount'] > 0) {
            echo json_encode(['exists' => true]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
?>
