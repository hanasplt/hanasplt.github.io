<?php

    include '../../../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $eventID = isset($_POST['event_id']) ? intval($_POST['event_id']) : null;

    if (!$eventID) {
        echo json_encode(['success' => false, 'message' => 'Event ID is required.']);
        exit;
    }

    try {
        error_log("Fetching teams for event ID: " . $eventID);

        $query = "CALL sp_getTeamInEvent(?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $eventID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $teams = [];
            while ($team = $result->fetch_assoc()) {
                $teams[] = $team;
            }

            echo json_encode(['success' => true, 'teams' => $teams]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No teams found for the specified event ID.']);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Error fetching teams: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching teams: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }

?>
