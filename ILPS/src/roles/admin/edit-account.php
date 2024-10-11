<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../admin/verifyLoginSession.php'; // logged in or not
require_once '../../../config/encryption.php';

$conn = require_once '../../../config/db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accId = $_SESSION['userId'];

    $userId = $_SESSION['ID'];
    $firstName = ucwords($_POST['firstName']);
    $middleName = ucfirst($_POST['middleName']);
    $lastName = ucfirst($_POST['lastName']);
    $suffix = ucfirst($_POST['suffix']);
    $email = $_POST['email'];
    $encryptedPassword = $_POST['password'];
    $password = encrypt($encryptedPassword, $encryption_key);
    $type = $_POST['sort'];

    // Retrieve all acount for duplication check
    $stmt = $conn->prepare("CALL sp_getAllAcc");
    $stmt->execute();
    $retval = $stmt->get_result();

    $accounts = array(); // Initialize array for storing email accounts
    if ($retval->num_rows > 0) {
        while ($row = $retval->fetch_assoc()) {
            $dbemail = $row['email'];
            $id = $row['userId'];
            $emails[] = array("email" => $dbemail, "userid" => $id);
        }
    }
    $stmt->free_result();
    $stmt->close();

    $found = false; // Initialize a variable for checking if there's a duplicate
    foreach ($emails as $acc) {
        if ($acc['email'] == $email && $acc['userid'] != $userId) {
            $found = true; // Duplicate found, won't edit the account
            echo json_encode(array("status" => "error", "message" => "This account already exists!"));
            exit;
        }
    }

    // Not a duplicate, updates the account
    if (!$found) {
        $stmt = $conn->prepare("CALL sp_editAcc(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssssss",
            $userId,
            $firstName,
            $middleName,
            $lastName,
            $suffix,
            $email,
            $password,
            $type
        );

        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Updated the account of $firstName $lastName (Acc. #: $userId)";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            echo json_encode(array("status" => "success", "message" => "Record updated successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Error: " . $stmt->error));
        }
        $stmt->close();
    }


    $conn->close();
}
