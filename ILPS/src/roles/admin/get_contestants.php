<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

$conn = require_once '../../../config/db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['evId'];
    $type = $_POST['type'];
    $eventname = $_POST['eventname'];

    $sql = "CALL sp_getEventContestant(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

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
                echo '<td><i class="fa-solid fa-trash-can delete-icon" data-cont="'.$conId.'" data-event-name="'.$eventname.'" data-id="'.$teamid.'" style="cursor: pointer;"></i></td>';
                echo '</tr>';
            } else {
                echo '<tr>';
                echo '<td>' . $contNum . '</td>';
                echo '<td>' . $team . '</td>';
                echo '<td><i class="fa-solid fa-trash-can delete-icon" data-cont="'.$conId.'" data-event-name="'.$eventname.'" data-id="'.$teamid.'" style="cursor: pointer;"></i></td>';
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
    
}
$conn->close();
?>
