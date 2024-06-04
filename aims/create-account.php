<?php
    include 'encryption.php';

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ilps";

    header('Content-Type: application/json');

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userId = $_POST['userId'];
        $firstName = $_POST['firstName'];
        $middleName = $_POST['middleName'];
        $lastName = $_POST['lastName'];
        $suffix = $_POST['suffix'];
        $password = encrypt($_POST['password'], $encryption_key);
        $type = $_POST['sort'];

        $stmt = $conn->prepare("CALL sp_getAllAcc");
        $stmt->execute();
        $retval = $stmt->get_result();

        $accounts = array();
        if ($retval->num_rows > 0) {
            while ($row = $retval->fetch_assoc()) {
                $ids = decrypt($row['userId'], $encryption_key);
                $accounts[] = array("id" => $ids);
            }
        }
        $stmt->free_result();
        $stmt->close();
        
        $found = false;
        foreach ($accounts as $acc) {
            if ($acc['id'] == $userId) {
                $found = true;
                echo json_encode(array("status" => "error", "message" => "This account already exists!"));
                break;
            }
        }

        if(!$found) {
            $userId = encrypt($_POST['userId'], $encryption_key);

            $stmt = $conn->prepare("CALL sp_insertAcc(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $userId, $firstName, $middleName, $lastName, $suffix, $password, $type);

            if ($stmt->execute()) {
                echo json_encode(array("status" => "success", "message" => "New account created successfully!"));
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        }

        $stmt->free_result();
        $stmt->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid request method"));
    }

    $conn->close();
?>