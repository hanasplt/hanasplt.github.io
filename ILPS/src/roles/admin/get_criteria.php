<?php

$conn = require_once '../../../config/db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];
    $event = $_POST['eventname'];

    $sql = "CALL sp_getCriteria(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="accounts-title" style="margin-left: 0vw;">';
    echo '<p id="event">Criteria Table</p>';
    echo '</div>';

    echo '<table class="contestantTable">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>No.</th>';
    echo '<th>Criteria</th>';
    echo '<th>Percentage</th>';
    echo '<th>Action</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        $count = 1;

        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            $id = $row['criteriaId'];
            $cri = $row['criteria'];
            $pts = $row['percentage'];

            echo '<tr>';
            echo '<td>' . $count++ . '</td>';
            echo '<td>' . $cri . '</td>';
            echo '<td>' . $pts . '</td>';
            echo '<td>
                    <i class="fa-solid fa-pen-to-square edit-icon-cri" data-id="'.$id.'"  data-name="'.$cri.'" data-event-name="'.$event.'" data-criteria="'.$cri.'" data-pts="'.$pts.'" data-event-id="'.$evid.'" onclick="openEditCriModal(this)" style="cursor: pointer;"></i>
                    <i class="fa-solid fa-trash-can delete-icon-cri" data-id="'.$id.'" data-name="'.$cri.'" data-event-name="'.$event.'" style="cursor: pointer;"></i>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4">No criterias.</td></tr>';
    }
    $result->free();
    $stmt->close();

    echo '</tbody>';
    echo '<table>';
}
?>