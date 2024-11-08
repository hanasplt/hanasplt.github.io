<?php

$conn = require_once '../../../config/db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$drop = "CALL sp_delLead()";
$stmt = $conn->prepare($drop);
$stmt->execute();
$stmt->close();

$sql = "CALL sp_getEvents()";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$events = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = array('event' => $row['eventID']);
    }
}
$result->free();
$stmt->close();



//fetch all contestants ranking in every events
$teams = array();
foreach ($events as $ev) {
    $evid = $ev['event'];

    $sql = "CALL sp_getTeamData(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $teams[] = array(
                'evid' => $row['eventId'],
                'conid' => $row['contestantId'],
                'team' => $row['team'],
                'rank' => $row['rank']
            );
        }
    }
    $result->free();
    $stmt->close();
}


$create = "create table leaderboard (no int primary key auto_increment, conId int, points int, 
            foreign key (conId) references contestant(contId));";
$stmt = $conn->prepare($create);
$stmt->execute();
$stmt->close();

//insert teams point base on their rank
foreach ($teams as $team) {
    $teamId = $team['conid'];
    $teamname = $team['team'];
    $num = $team['rank'];
    $evid = $team['evid'];

    $sql = "SELECT points FROM vw_eventscore WHERE rankNo = ? AND 
            eventCategory = (SELECT eventCategory FROM vw_events WHERE eventId = ?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $num, $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pts = $row['points'];

            $ins = "INSERT INTO leaderboard (no, conId, points) VALUES (NULL, ?, ?);";
            $insert_stmt = $conn->prepare($ins);
            $insert_stmt->bind_param("ii", $teamId, $pts);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
    }
    $result->free();
    $stmt->close();
}


// Get all event IDs
$sql_all_event_ids = "SELECT eventID FROM vw_events";
$result_all_events = $conn->query($sql_all_event_ids);

$events_data = []; // Array to hold event data with ranks
$team_performance = []; // Array to track teams' medals and scores

if ($result_all_events) {
    while ($row = $result_all_events->fetch_assoc()) {
        $event_id = $row['eventID'];

        // Call sp_getEvent for each event ID
        $sql_event = "CALL sp_getEvent(?)";
        $stmt_event = $conn->prepare($sql_event);
        $stmt_event->bind_param("i", $event_id);

        if ($stmt_event->execute()) {
            $result_event = $stmt_event->get_result();
            $event_details = $result_event->fetch_assoc(); // Get event details

            // Clear result for the next call
            $result_event->free();
        } else {
            echo "Error executing sp_getEvent: " . $stmt_event->error;
            continue; // Skip this iteration on error
        }

        // Clear statement to prepare for the next call
        $stmt_event->close();

        // Call sp_getData to retrieve rank data by event ID
        $sql_data = "CALL sp_getData(?)";
        $stmt_data = $conn->prepare($sql_data);
        $stmt_data->bind_param("i", $event_id);

        if ($stmt_data->execute()) {
            $result_data = $stmt_data->get_result();

            while ($data = $result_data->fetch_assoc()) {
                $team_name = $data['team'];
                $score = $data['score'];
                $rank = $data['rank'];

                // Initialize team performance data if it doesn't exist
                if (!isset($team_performance[$team_name])) {
                    $team_performance[$team_name] = [
                        'gold' => 0,
                        'silver' => 0,
                        'bronze' => 0,
                        'total_score' => 0,
                    ];
                }

                // Update medal counts based on rank
                switch ($rank) {
                    case 1:
                        $team_performance[$team_name]['gold']++;
                        break;
                    case 2:
                        $team_performance[$team_name]['silver']++;
                        break;
                    case 3:
                        $team_performance[$team_name]['bronze']++;
                        break;
                }

                // Accumulate the total score for the team
                $team_performance[$team_name]['total_score'] += $score;
            }

            // Clear result for the next call
            $result_data->free();
        } else {
            echo "Error executing sp_getData: " . $stmt_data->error;
            continue; // Skip this iteration on error
        }

        // Clear statement for sp_getData
        $stmt_data->close();
    }
} else {
    echo "Error fetching event IDs: " . $conn->error;
}

// Sort the team_performance array by total_score (highest to lowest)
uasort($team_performance, function ($a, $b) {
    return $b['total_score'] <=> $a['total_score'];
});

// Display the sorted teams with rank
$rank = 1; // Initialize rank counter
$output = '<tr>
<th class="rank-column">Rank</th>
<th class="name-column">Team Name</th>
<th class="gold-column"><img src="../../../public/assets/icons/gold-medal.png" class="medal-icon"></th>
<th class="silver-column"><img src="../../../public/assets/icons/silver-medal.png" class="medal-icon"></th>
<th class="bronze-column"><img src="../../../public/assets/icons/bronze-medal.png" class="medal-icon"></th>
<th class="points-column">Total Points</th>
</tr>';
foreach ($team_performance as $team_name => $performance) {
    $output .= '<tr>
                    <td>' . htmlspecialchars($rank) . '</td>
                    <td>' . htmlspecialchars($team_name) . '</td>
                    <td>' . htmlspecialchars($performance['gold']) . '</td>
                    <td>' . htmlspecialchars($performance['silver']) . '</td>
                    <td>' . htmlspecialchars($performance['bronze']) . '</td>
                    <td>' . htmlspecialchars($performance['total_score']) . '</td>
                </tr>';
    $rank++; // Increment rank for next team
}

if (count($team_performance) === 0) {
    // Inform user of no ranking yet
    $output .= '<tr><td colspan=6>No Ranking Available.</td></tr>';
}

echo $output;
