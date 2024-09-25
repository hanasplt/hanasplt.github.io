<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../admin/verifyLoginSession.php'; // Logged in or not
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

            $_SESSION['error'] = "Score Recorded Successfully!";
            header('Location: Sevents.php?event='.$eventId.'&name='.$eventname); // Return to previous page
            exit();
            
        } else {
            $_SESSION['error'] = "Error Recording Score!";
            header('Location: Sevents.php?event='.$eventId.'&name='.$eventname); // Return to previous page
            exit();
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error" .$e->getMessage();
        header('Location: Sevents.php?event='.$eventId.'&name='.$eventname); // Return to previous page
        exit();
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

            $_SESSION['error'] = "Score Updated Successfully!";
            header('Location: Sevents.php?event='.$eventId.'&name='.$eventname); // Return to previous page
            exit();

        } else {
            $_SESSION['error'] = "Error updating score!";
            header('Location: Sevents.php?event='.$eventId.'&name='.$eventname); // Return to previous page
            exit();
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error" .$e->getMessage();
        header('Location: Sevents.php?event='.$eventId.'&name='.$eventname); // Return to previous page
        exit();
    }
}

?>