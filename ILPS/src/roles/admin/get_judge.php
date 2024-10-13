<?php

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php';
require_once 'adminPermissions.php'; // Retrieves admin permissions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];
    $event = $_POST['eventname'];

    $sql = "CALL sp_getEventJudge(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display judge table - permitted to view
    if (in_array('judge_read', $admin_rights)) {
    echo '<div class="accounts-title" style="margin-left: 0vw;">';
    echo '<p id="event">Judge Table</p>';
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
            $id = $row['judgeNo'];
            $name = $row['firstName'];
            $lname = $row['lastName'];
            $fullname = $name .' '. $lname;

            echo '<tr>';
            echo '<td>' . $count++ . '</td>';
            echo '<td>' . $fullname . '</td>';

            // Display delete-icon - permitted to delete
            if (in_array('judge_delete', $admin_rights)) {
            echo '<td><i class="fa-solid fa-trash-can delete-icon-judge" data-id="'.$id.'" data-name="'.$fullname.'" data-event-name="'.$event.'" style="cursor: pointer;"></i></td>';
            } else { // Display message - not permitted to delete
                echo '<td style="color: darkgrey;">Feature denied.</td>';
            }
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No judges.</td></tr>';
    }
    $result->free();
    $stmt->close();

    echo '</tbody>';
    echo '</table>';
    } else { // Display message - not permitted to view
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Judges.
            </div>
        ';
    }    
}
?>