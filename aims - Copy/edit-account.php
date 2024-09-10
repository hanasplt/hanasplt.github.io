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
    $firstName = ucwords($_POST['firstName']);
    $middleName = ucfirst($_POST['middleName']);
    $lastName = ucfirst($_POST['lastName']);
    $suffix = ucfirst($_POST['suffix']);
    $email = $_POST['email'];
    $encryptedPassword = $_POST['password'];
    $password = encrypt($encryptedPassword, $encryption_key);
    $type = $_POST['sort'];

    $stmt = $conn->prepare("CALL sp_getAllAcc");
    $stmt->execute();
    $retval = $stmt->get_result();

    $accounts = array();
    if ($retval->num_rows > 0) {
        while ($row = $retval->fetch_assoc()) {
            $dbemail = $row['email'];
            $id = $row['userId'];
            $emails[] = array("email" => $dbemail, "userid" => $id);
        }
    }
    $stmt->free_result();
    $stmt->close();
    
    $found = false;
    foreach ($emails as $acc) {
        if ($acc['email'] == $email && $acc['userid'] != $userId) {
            $found = true;
            echo json_encode(array("status" => "error", "message" => "This account already exists!"));
            exit;
        }
    }

    if(!$found) {
        $stmt = $conn->prepare("CALL sp_editAcc(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $userId, $firstName, $middleName, $lastName, 
                        $suffix, $email, $password, $type);

        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Record updated successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Error: " . $stmt->error));
        }
        $stmt->close();
    }
    

    $conn->close();
}
?>
