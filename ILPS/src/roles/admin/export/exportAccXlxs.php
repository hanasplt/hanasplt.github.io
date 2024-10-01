<?php
    date_default_timezone_set('Asia/Manila');

    $conn = require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the database
        $sql = "CALL sp_getAllAcc()";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            $output .= '
                <table>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
            ';
            
            // Proceed populating data
            while ($row = $retval->fetch_assoc()) {
                $output .= '
                    <tr>
                        <td>'.$row['userId'].'</td>
                        <td>'.$row['firstName'].'</td>
                        <td>'.$row['middleName'].'</td>
                        <td>'.$row['lastName'].'</td>
                        <td>'.$row['suffix'].'</td>
                        <td>'.$row['email'].'</td>
                        <td>'.$row['type'].'</td>
                    </tr>
                ';
            }
            // end of while loop (done populating)

            $output .= '</table>';

        } else {
            $output .= '
                <table>
                    <tr>
                        <td colspan=4>No account/s exists.</td>
                    </tr>
                </table>
            ';
        }

        // Define header functions
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Account-[".$dt_exported."].xls");

        echo $output;
    }
?>