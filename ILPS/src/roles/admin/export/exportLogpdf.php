<?php
	require_once '../../../../resources/dompdf/autoload.inc.php';
	use Dompdf\Dompdf;

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

		$output .= '
			<style>
                * {
                    font-family: Arial, Helvetica, sans-serif;
                }
            </style>
			<h2 align="center">Audit Log of Year '.$year.'</h2></br>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Action</th>
                </tr>
        ';

        if ($retval->num_rows > 0) {
			// Populate table
			$count = 1;
			while ($row = $retval->fetch_assoc()) {
				$output .= '
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$count++.'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['date_on'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['fullname'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['actions'].'</td>
                    </tr>
                ';
			}
		} else {
			$output .= '
				<tr>
					<td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
						No logs found for year '.$year.'.
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
	$dompdf->stream("ILPS-AuditLog(".$year.") [".$dt_exported."].pdf");
	
?>