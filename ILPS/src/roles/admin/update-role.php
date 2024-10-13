<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php'; // Database connection

$accId = $_SESSION['userId']; // User that's modifying this data

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        
        $id = $_POST['userId'];
        $role = $_POST['userRole'];
        $fullname = $_POST['fullname'];
        $permissions = ""; // Get checkbox from - role
        
        // Check what checkbox to retrieve
        if ($role == 'Admin') {
            $permissions = $_POST['admin_permissions'];
        } else if ($role == 'Committee') {
            $permissions = $_POST['committee_permissions'];
        } else if ($role == 'Judge') {
            $permissions = $_POST['judge_permissions'];
        }

        $val = [];
        foreach ($permissions as $rights) {
            // Store values in the val array
            $val[] = $conn->real_escape_string($rights); // Prevent SQL injection
        }

        // Combine values as comma-seperated
        $accessRights = implode(",", $val);

        $updateRole = "UPDATE vw_accounts SET permissions = ? WHERE userId = ?";

        $stmt = $conn->prepare($updateRole);
        $stmt->bind_param("si", $accessRights, $id);
        
        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Updated access rights of $fullname";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            // Return success message
            echo json_encode(['status' => 'success', 'message' => 'User\'s Role Updated Successfully!']);
        } else {
            // Return error message
            echo json_encode(['status' => 'error', 'message' => 'Unable to Update User\'s Role!']);
        }
        
    } catch (Exception $e) {
        // Return error message
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>