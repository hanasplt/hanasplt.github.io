<?php

$conn = require_once '../../../config/db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];
    $event = $_POST['eventname'];

    $sql = "CALL sp_getEventJudge(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

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
            $id = $row['judgeNo'];
            $name = $row['firstName'];

            echo '<tr>';
            echo '<td>' . $count++ . '</td>';
            echo '<td>' . $name . '</td>';
            echo '<td><i class="fa-solid fa-trash-can delete-icon-judge" data-id="'.$id.'" data-name="'.$name.'" data-event-name="'.$event.'" style="cursor: pointer;"></i></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No judges.</td></tr>';
    }
    $result->free();
    $stmt->close();

    echo '</tbody>';
    echo '</table>';
}
?>