<?php
if (isset($_POST['eventID'])) {
    $eventID = $_POST['eventID'];

    $conn = require_once '../../../config/db.php'; // Include Database Connection

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve team information and rank for this event
    $sql = "CALL sp_getData(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to store teams info.
    $teams = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Store team info.
            $teams[] = array('event' => $row['eventId'], 'team' => $row['team'], 'rank' => $row['rank']);
        }
    }
    $result->free();
    $stmt->close();

    // Display table header
    $output = '<tr>
                    <th class="rank-column">Rank</th>
                    <th class="name-column">Team Name</th>
                    <th class="points-column">Points</th>
                </tr>';


    if (count($teams) === 0) {
        // Inform user of no ranking yet
        $output .= '<tr><td colspan=3>No Ranking Available.</td></tr>';
    }

    // Loop to get ranking, esp on Socio-Cultural score
    foreach ($teams as $team) {
        $teamname = $team['team'];
        $num = $team['rank'];
        $evid = $team['event'];

        // Retrieve points based on team's rank
        $sql = "CALL sp_getRanking(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $num, $evid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pts = $row['points'];
                $output .= '<tr>
                            <td>' . $num . '</td>
                            <td><img src="../../../public/assets/icons/sample.png"> ' . $teamname . '</td>
                            <td>' . $pts . '</td>
                        </tr>';
            }
        }

        $result->free();
        $stmt->close();
    }

    echo $output;
}
