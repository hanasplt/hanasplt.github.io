<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once '../../../config/encryption.php';
require_once '../../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    try {
        $userId = $_POST['userId'];
    
        $sql = "CALL sp_getAnAcc(?)"; // Retrieve account information
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
    
            $rights = explode(',', $user['permissions']); // Separate comma-separated string
    
            echo json_encode([
                "status" => "success",
                "type" => $user['type'], 
                "fullname" => $user['firstName'].' '.$user['lastName']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "User not found $userId"]);
        }
    
        $stmt->close();

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No userId provided"]);
}

$conn->close();
?>