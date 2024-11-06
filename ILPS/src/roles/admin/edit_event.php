<?php
include '../../../config/db.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();

$accId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Required fields
    $event_id = $_POST['eventId'];
    $day_id = $_POST['dayId'];
    $time = $_POST['time'];
    $type = $_POST['type'];
    $activity = $_POST['activity'];
    $location = $_POST['location'];
    $status = $_POST['status'];


    if ($type === 'sports' || $type === 'Sports' ){
        $gameNo = !empty($_POST['gameNo']) ? (int)$_POST['gameNo'] : null;
        if(!is_null($gameNo)){
            $teamA = isset($_POST['teamA']) ? (int)$_POST['teamA'] : null;
            $teamB = isset($_POST['teamB']) ? (int)$_POST['teamB'] : null;
        } else {
            $teamA = null;
            $teamB = null;
        }
    }
    

    if ($type === 'Socio-Cultural'){
        $gameNo = null;
        $teamA = null;
        $teamB = null;
    }

    if ($type === 'Others'){
        $gameNo = null;
        $teamA = null;
        $teamB = null;
    }

    if (empty($event_id) || empty($day_id) || empty($time) || empty($type) || empty($activity) || empty($location) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        $updateQuery = "CALL sp_updateEventSched(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($updateQuery);
        
        $stmt->bind_param('ssssssssi', $time, $type, $activity, $location, $gameNo, $teamA, $teamB, $status, $event_id);

        if ($stmt->execute()) {
            $updatedData = [
                'event_id' => $event_id,
                'day_id' => $day_id,
                'time' => $time,
                'type' => $type,
                'activity' => $activity,
                'location' => $location,
                'gameNo' => $gameNo,
                'teamA' => $teamA,
                'teamB' => $teamB,
                'status' => $status
            ];

            // Insert action in the logs
            $action = "
                Updated scheduled event(id: $event_id) in day-id: $day_id. 
                Starts on $time at $location, event type: $type--$activity,
                status $status.
            ";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            echo json_encode([
                'success' => true, 
                'message' => 'Event updated successfully.', 
                'updated_data' => $updatedData
            ]);
        } else {
            throw new Exception('Failed to update event.');
        }
        

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
}
?>
