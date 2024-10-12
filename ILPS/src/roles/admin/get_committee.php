<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php';
require_once 'adminPermissions.php'; // Retrieves admin permissions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evId = $_POST['evid'];
    $evname = $_POST['eventname'];

    $sql = "CALL sp_getEventComt(?)";    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evId);    
    $stmt->execute();
    $result = $stmt->get_result();

    if (in_array('committee_read', $admin_rights)) {
    echo '<div class="accounts-title" style="margin-left: 0vw;">';
    echo '<p id="event">Committee Table</p>';
    echo '</div>';

    echo '<table class="contestantTable">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>No.</th>';
    echo '<th>Name</th>';
    echo '<th>Action</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        $count = 1;

        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            $id = $row['comNo'];
            $name = $row['firstName'];
            $lname = $row['lastName'];
            $fullname = $name . ' ' . $lname;

            echo '<tr>';
            echo '<td>' . $count++ . '</td>';
            echo '<td>' . $fullname . '</td>';

            // Displays delete icon - permitted
            if (in_array('committee_delete', $admin_rights)) {
            echo '<td><i class="fa-solid fa-trash-can delete-icon-faci" data-id="'.$id.'" data-name="'.$fullname.'" data-event-name="'.$evname.'" style="cursor: pointer;"></i></td>';
            } else {
            echo '<td style="color: darkgrey;">Feature denied.</td>';
            }
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No Committee.</td></tr>';
    }
    $result->free();
    $stmt->close();

    echo '</tbody>';
    echo '</table>';
    } else {
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Committees.
            </div>
        ';
    }
}
?>
