<?php
	require_once '../../../../resources/dompdf/autoload.inc.php';
	use Dompdf\Dompdf;
	use Dompdf\Options;

	date_default_timezone_set('Asia/Manila');
    require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
	$dt_exported = date("Y-m-d H:i:s"); // Extend as file name
	$year = date('Y');
	$imageData = base64_encode(file_get_contents('../../../../public/assets/icons/useologo.png'));
	$src = 'data:image/png;base64,'.$imageData;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Retrieve data from the database
		try {
			$sql = "CALL sp_displayLog()";

			$stmt = $conn->prepare($sql);
			$stmt->execute();

			$retval = $stmt->get_result();

			$output .= '
				<style>
					* {
						font-family: Arial, Helvetica, sans-serif;
					}
					.header {
						top: 0px;
						left: 0px;
						right: 0px;
						height: 150px;
						text-align: center;
					}
					.content {
						page-break-after: always;
					}
					.header-image {
						width: 100px;
						margin: 0 auto;
					}
					.usep-name {
						font-family: Old English Text MT;
						font-size: 20px;
					}
					.page-number:before {
						content: counter(page);
					}
					table {
						width: 100%;
						border-collapse: collapse;
					}
					th, td {
						border: 1px solid #ddd;
						padding: 8px;
						font-size: 15px;
						text-align: left;
					}
					.vision-line {
						border-top: 1px solid black;
					}
					main {
						margin-top: 80px;
					}
					img {
						width: 140px;
					}
				</style>

				<div class="header">
					<img src="'.$src.'" class="header-image" alt="usep logo"><br>
					<span class="usep-name">University of Southeastern Philippines</span><br>
					<i>Office of the Student Affairs and Services<br>Tagum-Mabini Campus</i><br>
					<h2 style="margin: 5px 0;">Audit Log of Year '.$year.'</h2>
				</div>
				
				<main>
					<table>
						<tr>
							<th>Date</th>
							<th>Name</th>
							<th>Action</th>
						</tr>
			';

			if ($retval->num_rows > 0) {
				// Populate table
				while ($row = $retval->fetch_assoc()) {
					$date = new DateTime($row['date_on']);
            		$formatted_date = $date->format('M j, Y \a\t g:i A');
					
					$output .= '
						<tr>
							<td>'.$formatted_date.'</td>
							<td>'.$row['fullname'].'</td>
							<td>'.$row['actions'].'</td>
						</tr>
					';
				}
			} else {
				$output .= '
					<tr>
						<td colspan="3">
							No logs found for year '.$year.'.
						</td>
					</tr>
				';
			}
			$output .= '</table></main>';

		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	$options = new Options();
	$options->setChroot(__DIR__);

	$dompdf = new Dompdf($options);
	$dompdf->loadHtml($output);
	$dompdf->setPaper("A4", "Portrait");
	$dompdf->render();
	$dompdf->stream("ILPS-AuditLog(".$year.") [".$dt_exported."].pdf");
	
?>