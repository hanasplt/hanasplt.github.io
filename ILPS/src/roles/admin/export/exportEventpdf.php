<?php
	require_once '../../../../resources/dompdf/autoload.inc.php';
	use Dompdf\Dompdf;

	date_default_timezone_set('Asia/Manila');
    $conn = require_once '../../../../config/db.php'; // Database connection

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

        try {
            if ($resullt->num_rows > 0) {
                // Display data
                $row = $resullt->fetch_assoc();
                $type = $row['eventType']; // For conditional statement
                $eventname = $row['eventName']; // For file

                $output .= '
                    <style>
                        * {
                            font-family: Arial, Helvetica, sans-serif;
                        }
                    </style>
                    <p align="center">
                        <b style="font-size: 30px;">'.$eventname.'</b></br>
                        '.$row['eventType'].' - '.$row['eventCategory'].'</br>
                        (A.Y. '.$year.'-'.($year+1).')
                    </p>
                ';
            } 

            $resullt->free();
            $stmt->close();
            
            // Retrieve contestant for this event
            $getCont = "CALL sp_getEventContestant(?)";
            $stmt = $conn->prepare($getCont);
            $stmt->bind_param("i", $evid);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Validate the table(s) to display
            if ($type == 'Sports') {
                // Contestant table header
                $output .= '
                <h4 align="left">Contestants</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 40px;">ID</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Name</th>
                            </tr>
                ';

                if ($result->num_rows > 0) {
                    // Display contestants
                    $count = 1;

                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$count++.'</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['team'].'</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <tr>
                            <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                                No contestant/s.
                            </td>
                        </tr>
                    ';
                }
                $output .= '</table></br>';

                $result->free();
                $stmt->close();
        
                // Display Committee table
                $sql = "CALL sp_getEventComt(?)";  
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evid);    
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Table header
                $output .= '
                    <h4 align="left">Committees</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 40px;">No.</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Name</th>
                        </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$count++.'</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'
                                    .$row['firstName'].' '.$row['lastName']. 
                                '</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                            No Committee/s.
                        </td>
                    ';
                }
                $output .= '</table></br>';
            }
            
            else if ($type == 'Socio-Cultural') {
                // Contestant table header
                $output .= '
                    <h4 align="left">Contestants</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 50px;">Contestant No.</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Name</th>
                            </tr>
                ';

                if ($result->num_rows > 0) {
                    // Display contestants
                    $count = 1;

                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['contNo'].'</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['team'].'</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <tr>
                            <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                                No contestant/s.
                            </td>
                        </tr>
                    ';
                }
                $output .= '</table></br>';

                $result->free();
                $stmt->close();
        
                // Display Judge and Criteria Table

                // Retrieve Judge Table
                $sql = "CALL sp_getEventJudge(?)";
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evid);
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Table header
                $output .= '
                    <h4 align="left">Judges</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left; width: 50px;">No.</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Name</th>
                        </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate Table
                        $output .= '
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$count++.'</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'
                                    .$row['firstName'].' '.$row['lastName'].
                                '</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                            No Judge/s.
                        </td>
                    ';
                }
    
                $output .= '</table></br>';
                $result->free();
                $stmt->close();
    
                // Retrieve Criteria Table
                $sql = "CALL sp_getCriteria(?)";
    
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evid);
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Table header
                $output .= '
                    <h4 style="align: left;">Criterias</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">No.</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Criteria</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Percentage</th>
                        </tr>
                ';
    
                if ($result->num_rows > 0) {
                    $count = 1;
    
                    while ($row = $result->fetch_assoc()) {
                        // Populate table
                        $output .= '
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$count++.'</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['criteria'].'</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['percentage'].'</td>
                            </tr>
                        ';
                    }
                } else {
                    $output .= '
                        <tr>
                            <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                                No Criteria/s.
                            </td>
                        </tr>
                    ';
                }
                $output .= '</table>'; // end of table
            }

        } catch (Exception $e) {
            echo 'Error: '. $e->getMessage();
        } 
	}

	$dompdf = new DOMPDF();
	$dompdf->loadHtml($output);
	$dompdf->setPaper("A4", "Portrait");
	$dompdf->render();
	$dompdf->stream("ILPS-Event".$eventname." [".$dt_exported."].pdf");
	
?>