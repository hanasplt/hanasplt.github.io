<?php
	require_once '../../../../resources/dompdf/autoload.inc.php';
	use Dompdf\Dompdf;

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

		$output .= '
			<style>
                * {
                    font-family: Arial, Helvetica, sans-serif;
                }
            </style>
			<h2 align="center">Score Report</h2></br>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Event Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Team Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Points</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Action</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Action At</th>
                </tr>
        ';

        if ($retval->num_rows > 0) {
			// Populate table
			$count = 1;
			while ($row = $retval->fetch_assoc()) {
				$output .= '
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['eventName'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['teamName'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['total_score'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['action_made'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['action_at'].'</td>
                    </tr>
                ';
			}
		} else {
			$output .= '
				<tr>
					<td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
						No reports exists.
					</td>
				</tr>
			';
		}
		$output .= '</table>';
	}

	$dompdf = new DOMPDF();
	$dompdf->loadHtml($output);
	$dompdf->setPaper("A4", "Portrait");
	$dompdf->render();
	$dompdf->stream("ILPS-Reports-_-[".$dt_exported."].pdf");
	
?>