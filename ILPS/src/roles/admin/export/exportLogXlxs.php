<?php
    date_default_timezone_set('Asia/Manila');
    $conn = require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['yearFilter'])) {
        $year = $_POST['yearFilter'];

        // Retrieve data from the database
        $sql = "CALL sp_displayLog(?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $year);
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
            $count = 1;
            while ($row = $retval->fetch_assoc()) {
                $output .= '
                    <tr>
                        <td>'.$count++.'</td>
                        <td>'.$row['date_on'].'</td>
                        <td>'.$row['fullname'].'</td>
                        <td>'.$row['actions'].'</td>
                    </tr>
                ';
            }
            // end of while loop (done populating)

            $output .= '</table>';

        } else {
            $output .= '
                <table>
                    <tr>
                        <td colspan=4>No logs found for year '.$year.'.</td>
                    </tr>
                </table>
            ';
        }

        // Define header functions
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=AuditLog_".$year."-[".$dt_exported."].xls");

        echo $output;
    }
?>