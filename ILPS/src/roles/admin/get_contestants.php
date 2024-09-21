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

    $sql = "CALL sp_getEventContestant(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

?>

    <div class="accounts-title" style="margin-left: 0vw;">
        <p id="event">Contestant Table</p>
    </div>

    <table id="<?php echo $db_evName; ?>Table" class="contestantTable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- CONTESTANT TABLE -->
        </tbody>
    </table>

<?php
    if ($result->num_rows > 0) {
        $count = 1;

        while ($row = $result->fetch_assoc()) {
            $conId = $row['contId'];
            $teamid = $row['teamId'];
            $team = $row['team'];

            echo '<tr>';
            echo '<td>' . $count++ . '</td>';
            echo '<td>' . $team . '</td>';
            echo '<td><i class="fa-solid fa-trash-can delete-icon" data-cont="'.$conId.'" data-id="'.$teamid.'" style="cursor: pointer;"></i></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No contestants.</td></tr>';
    }
    $result->free();
    $stmt->close();
}
$conn->close();
?>
