<?php
    date_default_timezone_set('Asia/Manila');
    require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name
	$year = date('Y');
    $src = 'http://localhost:3000/ILPS/public/assets/icons/useologo.png';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the database
        try {
            $sql = "CALL sp_displayLog()";

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $retval = $stmt->get_result();

            if ($retval->num_rows > 0) {
                $output .= '
                    <style>
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                        th, td {
                            border: 1px solid #ddd; 
                            padding: 5px; 
                            text-align: left;
                        }
                        .header {
                            text-align: center;
                        }
                        #usep-name {
                            font-family: Old English Text MT;
                            font-size: 20px;
                            font-weight: bold;
                        }
                    </style>
                    <table>
                        <!-- Header Rows -->
                        <tr>
                            <th colspan="3" class="header">
                                <img src="'.$src.'" class="header-image" width="120" height="120" alt="usep logo">
                            </th>
                        </tr>
                        <tr>
                            <th colspan="3" class="header" id="usep-name">University of Southeastern Philippines</th>
                        </tr>
                        <tr>
                            <td colspan="3" class="header"><i>Office of the Student Affairs and Services</i></th>
                        </tr>
                        <tr>
                            <td colspan="3" class="header"><i>Tagum-Mabini Campus</i></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="header">Access Log as of ' . date("F j, Y") . '</th>
                        </tr>
                        <tr><th colspan="3"></th></tr>

                        <!-- Column Header -->
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>

                ';
                
                // Proceed populating dat
                while ($row = $retval->fetch_assoc()) {
                    $output .= '
                        <tr>
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
                            <td colspan="3">No logs found for year '.$year.'.</td>
                        </tr>
                    </table>
                ';
            }

            // Define header functions
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=AuditLog_".$year."-[".$dt_exported."].xls");

            echo $output;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
?>