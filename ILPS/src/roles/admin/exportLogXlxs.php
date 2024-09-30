<?php
    $conn = require_once '../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['exportlog_xsls'])) {
        // Retrieve data from the database
        $sql = "CALL sp_displayLog()";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            $output .= '
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
            ';
            
            // Proceed populating data
            while ($row = $retval->fetch_assoc()) {
                $output .= '
                    <tr>
                        <td>'.$row['logId'].'</td>
                        <td>'.$row['date_on'].'</td>
                        <td>'.$row['fullname'].'</td>
                        <td>'.$row['actions'].'</td>
                    </tr>
                ';
            }
            // end of while loop (done populating)

            $output .= '</table>';

            // Define header functions
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=AuditLog.xls");

            echo $output;
        }
    }
?>