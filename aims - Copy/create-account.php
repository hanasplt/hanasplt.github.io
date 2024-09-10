<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
    include 'encryption.php';
    header('Content-Type: application/json');

    $conn = include 'db.php';

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstName = ucwords($_POST['firstName']);
        $middleName = ucfirst($_POST['middleName']);
        $lastName = ucfirst($_POST['lastName']);
        $suffix = ucfirst($_POST['suffix']);
        $email = $_POST['email'];
        $password = encrypt($_POST['password'], $encryption_key);
        $type = $_POST['sort'];

        $stmt = $conn->prepare("CALL sp_getAllAcc");
        $stmt->execute();
        $retval = $stmt->get_result();

        $accounts = array();
        if ($retval->num_rows > 0) {
            while ($row = $retval->fetch_assoc()) {
                $dbemail = $row['email'];
                $emails[] = array("email" => $dbemail);
            }
        }
        $stmt->free_result();
        $stmt->close();
        
        $found = false;
        foreach ($emails as $acc) {
            if ($acc['email'] == $email) {
                $found = true;
                echo json_encode(array("status" => "error", "message" => "This account already exists!"));
                exit;
            }
        }

        if(!$found) {
            $stmt = $conn->prepare("CALL sp_insertAcc(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $firstName, $middleName, $lastName, $suffix, $email, $password, $type);

            if ($stmt->execute()) {
                echo json_encode(array("status" => "success", "message" => "New account created successfully!"));
            } else {
                throw new Exception("Failed to insert account: " . $stmt->error);
            }
        }

        $stmt->free_result();
        $stmt->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid request method"));
    }

    $conn->close();
?>