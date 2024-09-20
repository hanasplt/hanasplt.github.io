<?php
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);

    require_once '../../../config/sessionConfig.php'; // session Cookie
    require_once '../admin/verifyLoginSession.php'; // logged in or not
    require_once '../../../config/encryption.php'; // Encryp and/or decrypt data
    header('Content-Type: application/json');

    $conn = require_once '../../../config/db.php'; // Database connection

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    try {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $accId = $_SESSION['userId'];

            $firstName = ucwords($_POST['firstName']);
            $middleName = ucfirst($_POST['middleName']);
            $lastName = ucfirst($_POST['lastName']);
            $suffix = ucfirst($_POST['suffix']);
            $email = $_POST['email'];
            $password = encrypt($_POST['password'], $encryption_key);
            $type = $_POST['sort'];
    
            $stmt = $conn->prepare("CALL sp_getAllAcc"); // Retrieve all accounts
            $stmt->execute();
            $retval = $stmt->get_result();
    
            $accounts = array(); // Initialize array for holding email accounts
            if ($retval->num_rows > 0) {
                while ($row = $retval->fetch_assoc()) {
                    $dbemail = $row['email'];
                    $emails[] = array("email" => $dbemail); // Store emails in the array
                }
            }
            $stmt->free_result();
            $stmt->close();
            
            $found = false; // Initiliazed this variable for duplication checking
            foreach ($emails as $acc) {
                if ($acc['email'] == $email) {
                    $found = true; // There is a duplicate, won't proceed inserting account

                    echo json_encode(array("status" => "error", "message" => "This account already exists!"));
                    exit;
                }
            }
    
            if(!$found) { 
                // No duplication, account will be inserted
                $stmt = $conn->prepare("CALL sp_insertAcc(?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $firstName, $middleName, $lastName, $suffix, $email, $password, $type);
    
                if ($stmt->execute()) {
                    // Insert in the logs
                    $action = "Created the account of $firstName $lastName";
                    $insertLogAct = "CALL sp_insertLog(?, ?)";

                    $stmt = $conn->prepare($insertLogAct);
                    $stmt->bind_param("is", $accId, $action);
                    $stmt->execute();

                    echo json_encode(array("status" => "success", "message" => "New account created successfully!"));
                } else {
                    echo json_encode(array("status" => "error", "message" => "Unable to create account!"));
                }
            }
    
            $stmt->free_result();
            $stmt->close();
        } else {
            echo json_encode(array("status" => "error", "message" => "Invalid request method"));
        }

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }

    $conn->close();
?>