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
        // Retrieve the event date for the specified day ID
        $query = "SELECT * FROM scheduled_days WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $dayID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $eventDate = new DateTime($row['day_date']);
            $currentDate = new DateTime();

            error_log("Event Date: " . $eventDate->format('Y-m-d'));
            error_log("Current Date: " . $currentDate->format('Y-m-d'));

            if ($eventDate < $currentDate) {
                // If the date has passed, mark all events on that day as "Ended"
                $updateQuery = "UPDATE scheduled_eventstoday SET status = 'Ended' WHERE day_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("i", $dayID);
                $updateStmt->execute();

                error_log("Update affected rows: " . $updateStmt->affected_rows);

                if ($updateStmt->affected_rows > 0) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'All events on this day have been marked as "Ended".',
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No events were updated. Please check the day ID.',
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'The event date has not yet passed, no update was made.'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No date found for the specified day ID.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Error updating event status: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating event status: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }

?>
