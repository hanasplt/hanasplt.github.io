<?php
    date_default_timezone_set('Asia/Manila');
    $conn = require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the database
        $sql = "CALL sp_scoreReport()";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            $output .= '
                <table>
                    <tr>
                        <th>Event Name</th>
                        <th>Team Name</th>
                        <th>Points</th>
                        <th>Action</th>
                        <th>Action At</th>
                    </tr>
            ';
            
            // Proceed populating data
            while ($row = $retval->fetch_assoc()) {
                $output .= '
                    <tr>
                        <td>'.$row['eventName'].'</td>
                        <td>'.$row['teamName'].'</td>
                        <td>'.$row['total_score'].'</td>
                        <td>'.$row['action_made'].'</td>
                        <td>'.$row['action_at'].'</td>
                    </tr>
                ';
            }
            // end of while loop (done populating)

            $output .= '</table>';

        } else {
            $output .= '
                <table>
                    <tr>
                        <td colspan=4>No reports exists.</td>
                    </tr>
                </table>
            ';
        }

        // Define header functions
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Report-_-[".$dt_exported."].xls");

        echo $output;
    }
?>