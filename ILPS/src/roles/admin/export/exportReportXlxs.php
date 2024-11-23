<?php
    date_default_timezone_set('Asia/Manila');
    $conn = require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name
    $src = 'http://localhost:3000/ILPS/public/assets/icons/useologo.png';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the database
        $sql = "CALL sp_scoreReport()";

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
                        padding: 8px; 
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
                        <th colspan="5" class="header">
                            <img src="'.$src.'" class="header-image" width="120" height="120" alt="usep logo">
                        </th>
                    </tr>
                    <tr>
                        <th colspan="5" class="header" id="usep-name">University of Southeastern Philippines</th>
                    </tr>
                    <tr>
                        <td colspan="5" class="header"><i>Office of the Student Affairs and Services</i></th>
                    </tr>
                    <tr>
                        <td colspan="5" class="header"><i>Tagum-Mabini Campus</i></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="header">Score Report as of ' . date("F j, Y") . '</th>
                    </tr>
                    <tr><th colspan="5"></th></tr>

                    <!-- Column Headers -->
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
                $date = new DateTime($row['action_at']);
                $formatted_date = $date->format('M j, Y \a\t g:i A');
                
                $output .= '
                    <tr>
                        <td>'.$row['eventName'].'</td>
                        <td>'.$row['teamName'].'</td>
                        <td>'.$row['total_score'].'</td>
                        <td>'.$row['action_made'].'</td>
                        <td>'.$formatted_date.'</td>
                    </tr>
                ';
            }
            // end of while loop (done populating)

            $output .= '</table>';

        } else {
            $output .= '
                <table>
                    <tr>
                        <td colspan="5">No reports exists.</td>
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