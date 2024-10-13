<?php
// Call this to retrieve admin's permissions
try {
    $getJudge = "CALL sp_getAnAcc(?)";

    $iddd = $_SESSION['userId'];
    $stmt = $conn->prepare($getJudge);
    $stmt->bind_param("i", $iddd);
    $stmt->execute();
    $retname = $stmt->get_result();

    $row = $retname->fetch_assoc();
    
    $judge_rights = explode(',', $row['permissions']);

    $retname->free();
    $stmt->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}