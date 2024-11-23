<?php
require_once '../../../../resources/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('Asia/Manila');
require_once '../../../../config/db.php';

$output = '';
$dt_exported = date("Y-m-d H:i:s");
$imageData = base64_encode(file_get_contents('../../../../public/assets/icons/useologo.png'));
$src = 'data:image/png;base64,'.$imageData;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
    $sql = "CALL sp_scoreReport()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $retval = $stmt->get_result();

    $output .= '
        <html>
        <head>
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
        </head>
        <body>
            <div class="header">
                <img src="'.$src.'" class="header-image" alt="usep logo"><br>
                <span class="usep-name">University of Southeastern Philippines</span><br>
                <i>Office of the Student Affairs and Services<br>Tagum-Mabini Campus</i><br>
                <h2 style="margin: 5px 0;">Score Report</h2>
            </div>

            <main>
                <table>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Team Name</th>
                            <th>Points</th>
                            <th>Action</th>
                            <th>Action At</th>
                        </tr>
                    </thead>
                    <tbody>';

    if ($retval->num_rows > 0) {
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
                        </tr>';
        }
    } else {
        $output .= '<tr><td colspan="5">No reports exist.</td></tr>';
    }
    
    $output .= '
                    </tbody>
                </table>
            </main>
        </body>
        </html>';
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

$options = new Options();
$options->setChroot(__DIR__);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($output);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("ILPS-Reports-_-[".$dt_exported."].pdf");
?>