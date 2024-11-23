<?php
    date_default_timezone_set('Asia/Manila');

    session_start();
    require_once '../../../../config/db.php'; // Database connection
    require_once '../adminPermissions.php'; // Retrieves admin permissions

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name
    $year = date("Y"); // For display purposes, Academic Year
    $src = 'http://localhost:3000/ILPS/public/assets/icons/useologo.png';

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

        try {
            if ($resullt->num_rows > 0) {
                // Display data
                $row = $resullt->fetch_assoc();
                $type = $row['eventType']; // For conditional statement
                $eventname = $row['eventName']; // For file
    
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
                    <tr><th colspan="3"></th></tr>
                    <tr>
                        <th colspan="3" class="header">'.$row['eventName'].'</th>
                    </tr>
                    <tr>
                        <td colspan="3" class="header">'.$row['eventType'].' - '.$row['eventCategory'].'</th>
                    </tr>
                    <tr>
                        <td colspan="3" class="header">A.Y. '.$year.'-'.($year+1).'</th>
                    </tr>
                    <tr><th colspan="3"></th></tr>
                ';
            }
    
            $resullt->free();
            $stmt->close();
    
            
            // Validate which table(s) to display
            if ($type == 'Sports') {
                // Retrieve contestant for this event
                if (in_array('contestant_read', $admin_rights)) { // Export if permitted
                $getCont = "CALL sp_getEventContestant(?)";
                $stmt = $conn->prepare($getCont);
                $stmt->bind_param("i", $evid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $output .= '
                    <tr>
                        <td colspan="3" style="font-weight: bold;">CONTESTANTS</td>
                    </tr>
                ';
        
                if ($result->num_rows > 0) {
                    // Display contestants
                    $count = 1;
        
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td colspan="3">'.$row['team'].'</td>
                            </tr>
                        ';
                    }
                }
                $output .= '<tr><th colspan="3"></th></tr>';
        
                $result->free();
                $stmt->close();
                }

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
                        <td colspan="3" style="font-weight: bold;">COMMITTEES</td>
                    </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td colspan="3">
                                    '.$row['firstName'].' '.$row['lastName'].'
                                </td>
                            </tr>
                        ';
                    }
                }
                }
            }
            
            else if ($type == 'Socio-Cultural') {
                // Display Contestant table with Judge and Criteria Table
    
                // Retrieve contestant for this event
                if (in_array('contestant_read', $admin_rights)) { // Export if permitted
                $getCont = "SELECT vp.contNo, vt.teamName AS team
                            FROM vw_eventParti vp
                            INNER JOIN vw_teams vt ON vp.teamId = vt.teamId
                            WHERE vp.eventId = ?
                            ORDER BY vp.contNo ASC;";
                $stmt = $conn->prepare($getCont);
                $stmt->bind_param("i", $evid);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $output .= '
                    <tr>
                        <td colspan="3" style="font-weight: bold;">CONTESTANTS</td>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th colspan="2">Name</th>
                    </tr>
                ';
        
                if ($result->num_rows > 0) {
                    // Display contestants
                    $count = 1;
        
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td>'.$row['contNo'].'</td>
                                <td colspan="2">'.$row['team'].'</td>
                            </tr>
                        ';
                    }
                }
                $output .= '<tr><th colspan="3"></th></tr>';
        
                $result->free();
                $stmt->close();
                }

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
                        <td colspan="3" style="font-weight: bold;">JUDGES</td>
                    </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate Table
                        $output .= '
                            <tr>
                                <td colspan="3">
                                    '.$row['firstName']. ' '.$row['lastName'].'
                                </td>
                            </tr>
                        ';
                    }
                }
                }
    
                $output .= '<tr><th colspan="3"></th></tr>';
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
                        <td colspan="3" style="font-weight: bold;">
                            CRITERIA FOR JUDGING
                        </td>
                    </tr>
                    <tr>
                        <th>No.</th>
                        <th>Criteria</th>
                        <th>Percentage</th>
                    </tr>
                ';
    
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td>'.$row['criteriaId'].'</td>
                                <td>'.$row['criteria'].'</td>
                                <td>'.$row['percentage'].'</td>
                            </tr>
                        ';
                    }
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