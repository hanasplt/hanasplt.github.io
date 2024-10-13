<?php

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php';
require_once 'adminPermissions.php'; // Retrieves admin permissions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];
    $event = $_POST['eventname'];

    $sql = "CALL sp_getCriteria(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    if (in_array('criteria_read', $admin_rights)) {
    echo '<div class="accounts-title" style="margin-left: 0vw;">';
    echo '<p id="event">Criteria Table</p>';

    if ($result->num_rows > 0) {
        echo '
            <div>';
            if (in_array('criteria_update', $admin_rights)) {
        echo '
            <button type="button" id="openEditCriteriaPopup" data-evid="'.$evid.'" data-evname="'.$event.'" style="cursor: pointer;">
                <i class="fa-solid fa-pen-to-square edit-icon-cri"></i>
                Edit
            </button>';
            }
            if (in_array('criteria_delete', $admin_rights)) {
        echo '
            <button type="button" onclick="deleteCri('.$evid.', \''.$event.'\')" style="cursor: pointer;">
                <i class="fa-solid fa-trash-can"></i>
                Delete
            </button>';
            }
        echo '
            </div>
        ';
    }
    echo '</div>';

    echo '<table class="contestantTable">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>No.</th>';
    echo '<th>Criteria</th>';
    echo '<th>Percentage</th>';
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
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4">No criterias.</td></tr>';
    }
    $result->free();
    $stmt->close();

    echo '</tbody>';
    echo '<table>';
    } else {
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Criterias.
            </div>
        ';
    }
}
?>