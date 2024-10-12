<?php
// Call this to retrieve admin's permissions
try {
    $getAdmin = "CALL sp_getAnAcc(?)";

    $iddd = $_SESSION['userId'];
    $stmt = $conn->prepare($getAdmin);
    $stmt->bind_param("i", $iddd);
    $stmt->execute();
    $retname = $stmt->get_result();

    // Retrieve Admin Name
    $row = $retname->fetch_assoc();
    $admin_name = $row['firstName'];
    
    $admin_rights = explode(',', $row['permissions']);

    $retname->free();
    $stmt->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}