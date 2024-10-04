<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../../../config/sessionConfig.php'; // Session Cookie
$conn = require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get the requested page and year filter
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $year = isset($_POST['year']) ? $_POST['year'] : "";

    $limit = 10; 
    $offset = ($page - 1) * $limit; 

    $yearCondition = "";
    if (!empty($year)) {
        $yearCondition = "WHERE YEAR(al.date_on) = ?";
    }

    // Retrieve total log count based sa year nga gi filter
    $countQuery = "SELECT COUNT(*) as total FROM adminlogs al $yearCondition";
    $stmtCount = $conn->prepare($countQuery);
    
    if (!empty($year)) {
        $stmtCount->bind_param("i", $year);
    }
    
    $stmtCount->execute();
    $countResult = $stmtCount->get_result();
    $totalLogs = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalLogs / $limit); 

    $getLogs = "SELECT al.logId, al.date_on, 
                    CONCAT(a.firstName, ' ', 
                           a.lastName) AS fullname, 
                    al.actions 
             FROM vw_logs al 
             JOIN vw_accounts a ON al.userId = a.userId
             $yearCondition
             ORDER BY al.logId
             LIMIT ? OFFSET ?";

    $stmtLogs = $conn->prepare($getLogs);
    
    if (!empty($year)) {
        $stmtLogs->bind_param("iii", $year, $limit, $offset);
    } else {
        $stmtLogs->bind_param("ii", $limit, $offset);
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
