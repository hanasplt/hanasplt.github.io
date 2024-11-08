<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get the requested page and year filter
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $year = date('Y');

    $limit = 10; 
    $offset = ($page - 1) * $limit; 

    // Retrieve total log count based sa year nga gi filter
    $countQuery = "SELECT fn_getLogCount(?) AS totalLogs";
    $stmtCount = $conn->prepare($countQuery);
    $stmtCount->bind_param("i", $year);
    $stmtCount->execute();
    $countResult = $stmtCount->get_result();
    $totalLogs = $countResult->fetch_assoc()['totalLogs'];
    $totalPages = ceil($totalLogs / $limit); 

    $getLogs = "CALL sp_getLogs(?,?,?)";

    $stmtLogs = $conn->prepare($getLogs);
    
    if (!empty($year)) {
        $stmtLogs->bind_param("iii", $year, $limit, $offset);
    }

    $stmtLogs->execute();
    $retval = $stmtLogs->get_result();

    $logs = [];
    if ($retval->num_rows > 0) {
        while ($row = $retval->fetch_assoc()) {
            $logs[] = [
                'logId' => $row['logId'],
                'date_on' => $row['date_on'],
                'fullname' => $row['fullname'], 
                'actions' => $row['actions']
            ];
        }
    }

    // Return JSON response
    echo json_encode(['logs' => $logs, 'totalPages' => $totalPages]);
}

$conn->close();
?>
