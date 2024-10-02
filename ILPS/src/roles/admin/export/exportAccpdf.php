<?php
	require_once '../../../../resources/dompdf/autoload.inc.php';
	use Dompdf\Dompdf;

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
                <style>
                    * {
                        font-family: Arial, Helvetica, sans-serif;
                    }
                </style>
				<h2 align="center">ILPS Accounts Information</h2></br>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">First Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Middle Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Last Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Suffix</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Email</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Role</th>
                    </tr>
            ';

			// Populate table
			while ($row = $retval->fetch_assoc()) {
				$output .= '
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['userId'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['firstName'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['middleName'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['lastName'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['suffix'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['email'].'</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">'.$row['type'].'</td>
                    </tr>
                ';
			}
		} else {
			$output .= '
				<tr>
					<td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: left;">
						No Account/s exists.
					</td>
				</tr>
			';
		}
	}

	$dompdf = new DOMPDF();
	$dompdf->loadHtml($output);
	$dompdf->setPaper("A4", "Landscape");
	$dompdf->render();
	$dompdf->stream("ILPS-Accounts [".$dt_exported."].pdf");
	
?>