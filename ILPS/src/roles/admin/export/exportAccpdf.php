<?php
	require_once '../../../../resources/dompdf/autoload.inc.php';
	use Dompdf\Dompdf;
	use Dompdf\Options;

    date_default_timezone_set('Asia/Manila');
    require_once '../../../../config/db.php'; // Database connection

    $output = ''; // Initialize variable for compiling data output
    $dt_exported = date("Y-m-d H:i:s"); // Extend as file name
    $imageData = base64_encode(file_get_contents('../../../../public/assets/icons/useologo.png'));
    $src = 'data:image/png;base64,'.$imageData;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            // Retrieve data from the database
            $sql = "CALL sp_getAllAcc()";

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $retval = $stmt->get_result();

            $output .= '
            ';
            
            if ($retval->num_rows > 0) {
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
                        .header-image {
                            width: 100px;
                            margin: 0 auto;
                        }
                        .usep-name {
                            font-family: Old English Text MT;
                            font-size: 20px;
                        }
                        table {
                            width: 100%; border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #ddd; padding: 8px; text-align: left;
                        }
                    </style>
                    <div class="header">
                        <img src="'.$src.'" class="header-image" alt="usep logo"><br>
                        <span class="usep-name">University of Southeastern Philippines</span><br>
                        <i>Office of the Student Affairs and Services<br>Tagum-Mabini Campus</i><br>
                    </div>
                    <h2 align="center">Account Information</h2></br>
                    <table>
                        <tr>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Suffix</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                ';

                // Populate table
                while ($row = $retval->fetch_assoc()) {
                    if ($row['userId'] != 1) {
                        $output .= '
                            <tr>
                                <td>'.$row['firstName'].'</td>
                                <td>'.$row['middleName'].'</td>
                                <td>'.$row['lastName'].'</td>
                                <td>'.$row['suffix'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['type'].'</td>
                            </tr>
                        ';
                    }
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
        } catch (Exception $e) {
            die($e->getMessage());
        }
	}

	$options = new Options();
    $options->setChroot(__DIR__);

    $dompdf = new Dompdf($options);
	$dompdf->loadHtml($output);
	$dompdf->setPaper("A4", "Landscape");
	$dompdf->render();
	$dompdf->stream("ILPS-Accounts [".$dt_exported."].pdf");
	
?>