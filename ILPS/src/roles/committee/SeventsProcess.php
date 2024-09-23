<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = require_once '../../../config/db.php'; // Database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['scoreCon'])) {
    $eventId = $_POST['evId'];
    $eventname = $_POST['evname'];
    $teamId = $_POST['cont'];
    $conname = $_POST['conname'];
    $total = $_POST['score'];
    $id = $_POST['id'];

    try {
        $sql = "CALL sp_insertResultComt(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $eventId, $teamId, $id, $total);
    
        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Added score for the team $conname in the event $eventname.";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $id, $action);
            $stmt->execute();

            echo json_encode([
                'status' => 'success',
                'message' => 'Score Recorded Successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error Recording Score!'
            ]);
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: '. $e->getMessage()
        ]);
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changeScore'])) {
    $eventId = $_POST['evId'];
    $eventname = $_POST['evname'];
    $teamId = $_POST['cont'];
    $conname = $_POST['conname'];
    $total = $_POST['score'];
    $id = $_POST['id'];
 
    try {
        $sql = "CALL sp_updateScore(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("diii", $total, $id, $eventId, $teamId);
    
        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Updated score for the team $conname in the event $eventname.";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $id, $action);
            $stmt->execute();

            echo "Success";
        } else {
            echo "Error";
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        echo "Error" .$e->getMessage();
    }
}

?>