<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../../../config/sessionConfig.php';
require_once '../../../config/db.php';
require_once '../admin/verifyLoginSession.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $year = date('Y');

    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Get total logs count
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
        $displayId = $totalLogs - $offset;

        while ($row = $retval->fetch_assoc()) {
            $logs[] = [
                'logId' => $displayId,
                'date_on' => $row['date_on'],
                'fullname' => $row['fullname'],
                'actions' => $row['actions']
            ];
            $displayId--;
        }
    }
    // Return JSON response
    echo json_encode(['logs' => $logs, 'totalPages' => $totalPages]);
}

$conn->close();
?>
