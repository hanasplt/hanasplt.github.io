<?php
    date_default_timezone_set('Asia/Manila');

    session_start();
    require_once '../../../../config/db.php'; // Database connection
    require_once '../adminPermissions.php'; // Retrieves admin permissions

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name
    $year = date("Y"); // For display purposes, Academic Year

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['xp_eventId'])) {
        $evid = $_POST['xp_eventId'];

        // Retrieve event data for conditional statement
        $getEvent = "CALL sp_getEvent(?)";

        $stmt = $conn->prepare($getEvent);
        $stmt->bind_param("i", $evid);
        $stmt->execute();
        $resullt = $stmt->get_result();

        $type = '';
        $eventname = '';
        $output .= '<table>';

        try {
            if ($resullt->num_rows > 0) {
                // Display data
                $row = $resullt->fetch_assoc();
                $type = $row['eventType']; // For conditional statement
                $eventname = $row['eventName']; // For file
    
                $output .= '
                    <tr>
                        <td colspan="2" style="font-weight: bold;text-align: center;">'.$row['eventName'].'</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">'.$row['eventType'].' - '.$row['eventCategory'].'</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">A.Y. '.$year.'-'.($year+1).'</td>
                    </tr>
                    <tr></tr>
                ';
            }
    
            $resullt->free();
            $stmt->close();
    
            // Retrieve contestant for this event
            if (in_array('contestant_read', $admin_rights)) { // Export if permitted
            $getCont = "CALL sp_getEventContestant(?)";
            $stmt = $conn->prepare($getCont);
            $stmt->bind_param("i", $evid);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $output .= '
                <tr>
                    <td colspan="2" style="font-weight: bold;">Contestants</td>
                </tr>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                </tr>
            ';
    
            if ($result->num_rows > 0) {
                // Display contestants
                $count = 1;
    
                while ($row = $result->fetch_assoc()) {
                    // Populate table
                    $output .= '
                        <tr>
                            <td>'.$count++.'</td>
                            <td>'.$row['team'].'</td>
                        </tr>
                    ';
                }
            } else {
                $output .= '
                    <tr><td colspan="2">No contestant/s.</td></tr>
                ';
            }
            $output .= '<tr></tr>';
    
            $result->free();
            $stmt->close();
            }
    
            // Validate which table(s) to display
            if ($type == 'Sports') {
                // Display Committee table
                if (in_array('committee_read', $admin_rights)) { // Export if permitted
                $sql = "CALL sp_getEventComt(?)";  
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evid);    
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Table header
                $output .= '
                    <tr>
                        <td colspan="2" style="font-weight: bold;">Committees</td>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                    </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$row['firstName'].'</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <tr><td colspan="2">No Committee/s.</td></tr>
                    ';
                }
                }
            }
            
            else if ($type == 'Socio-Cultural') {
                // Display Judge and Criteria Table
    
                // Retrieve Judge Table
                if (in_array('judge_read', $admin_rights)) { // Export if permitted
                $sql = "CALL sp_getEventJudge(?)";
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evid);
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Table header
                $output .= '
                    <tr>
                        <td colspan="2" style="font-weight: bold;">Judges</td>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                    </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate Table
                        $output .= '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$row['firstName'].'</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <tr><td colspan="2">No Judge/s.</td></tr>
                    ';
                }
                }
    
                $output .= '<tr></tr>';
                $result->free();
                $stmt->close();
    
                // Retrieve Criteria Table
                if (in_array('criteria_read', $admin_rights)) { // Export if permitted
                $sql = "CALL sp_getCriteria(?)";
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evid);
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Table header
                $output .= '
                    <tr>
                        <td colspan="2" style="font-weight: bold;">Criterias</td>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Criteria</th>
                        <th>Percentage</th>
                    </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>'.$row['criteria'].'</td>
                                <td>'.$row['percentage'].'</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <tr><td colspan="3">No Criteria/s.</td></tr>
                    ';
                }
                }
            }
            
    
            $output .= '</table>'; // end of event table
        } catch (Exception $e) {
            echo 'Error:'. $e->getMessage();
        }

        // Define header functions
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Event".$eventname."-[".$dt_exported."].xls");

        echo $output;
    }
?>