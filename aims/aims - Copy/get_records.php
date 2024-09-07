<?php
if (isset($_POST['eventID'])) {
    $eventID = $_POST['eventID'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "ilps";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CALL sp_getData(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    $teams = array();
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $teams[] = array('event' => $row['eventId'], 'team' => $row['team'], 'rank' => $row['rank']);
        }
    }
    $result->free();
    $stmt->close();

    $output = '<tr>
                    <th class="rank-column">Rank</th>
                    <th class="name-column">Team Name</th>
                    <th class="points-column">Points</th>
                </tr>';


    foreach($teams as $team) {
        $teamname = $team['team'];
        $num = $team['rank'];
        $evid = $team['event'];

        echo "<script>alert('$teamname $num $evid')</script>";

        $sql = "CALL sp_getRanking(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $num, $evid);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pts = $row['points'];
                $output .= '<tr>
                            <td>' . $num . '</td>
                            <td><img src="assets/icons/sample.png"> ' . $teamname . '</td>
                            <td>' . $pts . '</td>
                        </tr>';
            }
        }
        $result->free();
        $stmt->close();
    }

    echo $output;
}
?>
