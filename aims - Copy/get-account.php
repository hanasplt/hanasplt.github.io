<?php
header('Content-Type: application/json');
include 'encryption.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilps";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}
session_start();

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $_SESSION['userId'] = $userId;
    $sql = "CALL sp_getAnAcc(?)";
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

        echo json_encode($user);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No userId provided"]);
}

$conn->close();
?>
