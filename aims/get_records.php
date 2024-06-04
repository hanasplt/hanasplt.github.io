<?php
if (isset($_POST['eventID'])) {
    $eventID = $_POST['eventID'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "ilpsystem";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CALL sp_getRecordFrom(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = '<tr>
                    <th class="rank-column">Rank</th>
                    <th class="name-column">Team Name</th>
                    <th class="points-column">Points</th>
                </tr>';

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $output .= '<tr>
                            <td>' . $rank . '</td>
                            <td><img src="assets/icons/sample.png"> ' . $row['teamName'] . '</td>
                            <td>' . $row['points'] . '</td>
                        </tr>';
            $rank++;
        }
    }

    $result->free();
    $stmt->close();
    $conn->close();

    echo $output;
}
?>
