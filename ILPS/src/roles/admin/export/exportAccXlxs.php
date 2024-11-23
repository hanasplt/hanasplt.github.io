<?php
    date_default_timezone_set('Asia/Manila');
    require_once '../../../../config/db.php';

    $output = '';
    $dt_exported = date("Y-m-d H:i:s");
    $src = 'http://localhost:3000/ILPS/public/assets/icons/useologo.png';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sql = "CALL sp_getAllAcc()";
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
                        <th colspan="6" class="header">
                            <img src="'.$src.'" class="header-image" width="120" height="120" alt="usep logo">
                        </th>
                    </tr>
                    <tr>
                        <th colspan="6" class="header" id="usep-name">University of Southeastern Philippines</th>
                    </tr>
                    <tr>
                        <td colspan="6" class="header"><i>Office of the Student Affairs and Services</i></th>
                    </tr>
                    <tr>
                        <td colspan="6" class="header"><i>Tagum-Mabini Campus</i></th>
                    </tr>
                    <tr>
                        <th colspan="6" class="header">Account List as of ' . date("F j, Y") . '</th>
                    </tr>
                    <tr><th colspan="6"></th></tr>
                    
                    <!-- Column Headers -->
                    <tr>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>';
            
            while ($row = $retval->fetch_assoc()) {
                if ($row['userId'] != 1) {
                    $output .= '
                        <tr>
                            <td>' . $row['firstName'] . '</td>
                            <td>' . $row['middleName'] . '</td>
                            <td>' . $row['lastName'] . '</td>
                            <td>' . $row['suffix'] . '</td>
                            <td>' . $row['email'] . '</td>
                            <td>' . $row['type'] . '</td>
                        </tr>';
                }
            }

            $output .= '
                    <!-- Footer -->
                    <tr><th colspan="6"></th></tr>
                    <tr>
                        <td colspan="6" style="text-align: left; border: none;">
                            Generated on: ' . date("F j, Y g:i A") . '
                        </td>
                    </tr>
                </table>';
        } else {
            $output .= '
                <table>
                    <tr>
                        <td colspan="6">No account/s exists.</td>
                    </tr>
                </table>';
        }

        // Headers for Excel download
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=USEP_Accounts_" . date("Y-m-d") . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $output;
    }
?>