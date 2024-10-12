<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php';
require_once 'adminPermissions.php'; // Retrieves admin permissions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['evId'];
    $type = $_POST['type'];
    $eventname = $_POST['eventname'];

    $sql = "CALL sp_getEventContestant(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (in_array('contestant_read', $admin_rights)) {
    echo '<div class="accounts-title" style="margin-left: 0vw;">';
    echo '<p id="event">Contestant Table</p>';
    echo '</div>';

    echo '<table class="contestantTable">';
    echo '<thead>';

    if ($type == "Sports") { // Display ascending number
        echo '<tr>';
        echo '<th>No.</th>';
        echo '<th>Name</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
    } else { // Display contestant no. stored in the database
        echo '<tr>';
        echo '<th>Contestant No.</th>';
        echo '<th>Name</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
    }

    if ($result->num_rows > 0) {
        $count = 1;

        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            $conId = $row['contId'];
            $contNum = $row['contNo'];
            $teamid = $row['teamId'];
            $team = $row['team'];

            if ($type == "Sports") {
                echo '<tr>';
                echo '<td>' . $count++ . '</td>';
                echo '<td>' . $team . '</td>';

                if (in_array('contestant_delete', $admin_rights)) {
                echo '<td><i class="fa-solid fa-trash-can delete-icon" data-cont="'.$conId.'" data-name="'.$team.'" data-event-name="'.$eventname.'" data-id="'.$teamid.'" style="cursor: pointer;"></i></td>';
                } else {
                    echo '<td style="color: darkgrey;">Feature denied.</td>';
                }
                echo '</tr>';
            } else {
                echo '<tr>';
                echo '<td>' . $contNum . '</td>';
                echo '<td>' . $team . '</td>';

                if (in_array('contestant_delete', $admin_rights)) {
                echo '<td><i class="fa-solid fa-trash-can delete-icon" data-cont="'.$conId.'" data-name="'.$team.'" data-event-name="'.$eventname.'" data-id="'.$teamid.'" style="cursor: pointer;"></i></td>';
                } else {
                    echo '<td style="color: darkgrey;">Feature denied.</td>';
                }
                echo '</tr>';
            }

        }
    } else {
        echo '<tr><td colspan="3">No contestants.</td></tr>';
    }

    $result->free();
    $stmt->close();

    echo '</tbody>';
    echo '</table>'; 
    } else {
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Contestants.
            </div>
        ';
    }
}
$conn->close();
?>
