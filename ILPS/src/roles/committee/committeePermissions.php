<?php
// Call this to retrieve committee's permissions
try {
    $getCommittee = "CALL sp_getAnAcc(?)";

    $iddd = $_SESSION['userId'];
    $stmt = $conn->prepare($getCommittee);
    $stmt->bind_param("i", $iddd);
    $stmt->execute();
    $retname = $stmt->get_result();

    $row = $retname->fetch_assoc();
    
    $comt_rights = explode(',', $row['permissions']);

    $retname->free();
    $stmt->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}