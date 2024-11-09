<?php

    date_default_timezone_set("Asia/Manila");
    include '../../../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $dayID = isset($_POST['day_id']) ? intval($_POST['day_id']) : null;

    if (!$dayID) {
        echo json_encode(['success' => false, 'message' => 'Day ID is required.']);
        exit;
    }

    try {
        $query = "SELECT * FROM scheduled_days WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $dayID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $eventDate = new DateTime($row['day_date']);
            $currentDate = new DateTime();

            $isEditable = ($eventDate->format('Y-m-d') >= $currentDate->format('Y-m-d'));

            echo json_encode([
                'success' => true,
                'editable' => $isEditable,
                'debug' => [
                    'eventDate' => $eventDate->format('Y-m-d'),
                    'currentDate' => $currentDate->format('Y-m-d')
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No date found for the specified day ID.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Error checking event date: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error checking event date: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }

?>
