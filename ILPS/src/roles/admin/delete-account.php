<?php
    require_once '../../../config/sessionConfig.php'; // session Cookie
    $conn = require_once '../../../config/db.php'; // database connection
    require_once '../admin/verifyLoginSession.php'; // logged in or not
    
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    $userId = $_POST['userId'];
    $accId = $_SESSION['userId'];


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CALL sp_delAcc(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    $response = array();

    if ($stmt->execute()) {
        // Insert in the logs
        $action = "Deleted the account no. $userId";
        $insertLogAct = "CALL sp_insertLog(?, ?)";

        $stmt = $conn->prepare($insertLogAct);
        $stmt->bind_param("is", $accId, $action);
        $stmt->execute();

        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header("HTTP/1.1 400 Bad Request");
}
?>
