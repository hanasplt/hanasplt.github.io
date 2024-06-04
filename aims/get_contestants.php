<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ilps";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['evId'];

    $sql = "CALL sp_getEventContestant(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $conId = $row['contId'];
            $teamid = $row['teamId'];
            $team = $row['teamName'];

            echo '<tr>';
            echo '<td>' . $conId . '</td>';
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
