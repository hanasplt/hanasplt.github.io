<?php
include 'encryption.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilps";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['userId'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $suffix = $_POST['suffix'];
    $encryptedPassword = $_POST['password'];
    $password = encrypt($encryptedPassword, $encryption_key);
    $type = $_POST['sort'];

    $stmt = $conn->prepare("CALL sp_editAcc(?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $userId, $firstName, $middleName, $lastName, $suffix, $password, $type);

    if ($stmt->execute()) {
        $stmt->close();

        $sql = "CALL sp_getAJudge(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            $retval->free();
            $stmt->close();

            $sqlUpdate = "UPDATE judges SET jugdeName = ? WHERE judgeId = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ss", $firstName, $userId);

            if ($stmtUpdate->execute()) {
            } else {
                echo json_encode(array("status" => "error", "message" => "Error updating judge: " . $stmtUpdate->error));
            }
            $stmtUpdate->close();
        }
        echo json_encode(array("status" => "success", "message" => "Record updated successfully"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error: " . $stmt->error));
    }

    $conn->close();
}
?>
