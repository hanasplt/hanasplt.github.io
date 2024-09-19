<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


require_once '../../../config/encryption.php';
$conn = require_once '../../../config/db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $_SESSION['ID'] = $userId;

    $sql = "CALL sp_getAnAcc(?)"; // Retrieve account information
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $decryptedPassword = decrypt($user['password'], $encryption_key);
        $decryptedId = decrypt($user['userId'], $encryption_key);
        $user['password'] = $decryptedPassword;
        $user['userId'] = $decryptedId;

        echo json_encode($user); // Send account information for display
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No userId provided"]);
}

$conn->close();
?>
