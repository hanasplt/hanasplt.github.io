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


// Get all events and their categories in one query
$sql_all_events = "SELECT eventID, eventCategory FROM vw_events";
$result_all_events = $conn->query($sql_all_events);

$team_performance = []; // Array to track teams' medals and scores

if ($result_all_events) {
    while ($row = $result_all_events->fetch_assoc()) {
        $event_id = $row['eventID'];
        $event_category = $row['eventCategory'];

        // Call sp_getData to retrieve all scores for the current event
        $sql_data = "CALL sp_getData(?)";
        $stmt_data = $conn->prepare($sql_data);
        
        if (!$stmt_data) {
            error_log("Error preparing sp_getData statement: " . $conn->error);
            continue;
        }

        $stmt_data->bind_param("i", $event_id);

        if ($stmt_data->execute()) {
            $result_data = $stmt_data->get_result();
            $event_scores = []; // To store scores per team for ranking
            
            while ($data = $result_data->fetch_assoc()) {
                $team_name = $data['team'];
                $score = $data['score'];
                $event_scores[$team_name] = $score;
            }

            // Free result before next iteration
            $result_data->free();
            $stmt_data->close();

            if (empty($event_scores)) {
                continue; // Skip if no scores for this event
            }

            // Rank the teams based on their scores (highest first)
            arsort($event_scores);
            $rank = 1;

            // Prepare ranking statement outside the loop
            $stmt_ranking = $conn->prepare("
                SELECT points 
                FROM vw_eventscore 
                WHERE rankNo = ? AND eventCategory = ?
            ");

            if (!$stmt_ranking) {
                error_log("Error preparing ranking statement: " . $conn->error);
                continue;
            }

            foreach ($event_scores as $team_name => $score) {
                $stmt_ranking->bind_param("is", $rank, $event_category);

                if ($stmt_ranking->execute()) {
                    $result_ranking = $stmt_ranking->get_result();
                    $ranking_data = $result_ranking->fetch_assoc();
                    $points = $ranking_data['points'] ?? 0;
                    $result_ranking->free();
                } else {
                    error_log("Error executing ranking query: " . $stmt_ranking->error);
                    continue;
                }

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

                // Accumulate the total score based on points
                $team_performance[$team_name]['total_score'] += $points;

                $rank++;
            }

            // Close ranking statement after processing all teams
            $stmt_ranking->close();
        } else {
            error_log("Error executing sp_getData: " . $stmt_data->error);
            $stmt_data->close();
            continue;
        }
    }

    // Free the main result set
    $result_all_events->free();
} else {
    error_log("Error fetching event IDs: " . $conn->error);
    echo '<tr><td colspan="6">Error fetching rankings.</td></tr>';
    exit;
}

// Sort the team_performance array by total_score (highest to lowest)
uasort($team_performance, function ($a, $b) {
    return $b['total_score'] <=> $a['total_score'];
});

// Generate table output
$output = '<tr>
    <th class="rank-column">Rank</th>
    <th class="name-column">Team Name</th>
    <th class="gold-column"><img src="../../../public/assets/icons/gold-medal.png" class="medal-icon" alt="Gold Medal"></th>
    <th class="silver-column"><img src="../../../public/assets/icons/silver-medal.png" class="medal-icon" alt="Silver Medal"></th>
    <th class="bronze-column"><img src="../../../public/assets/icons/bronze-medal.png" class="medal-icon" alt="Bronze Medal"></th>
    <th class="points-column">Total Points</th>
</tr>';

if (empty($team_performance)) {
    $output .= '<tr><td colspan="6">No Ranking Available.</td></tr>';
} else {
    $rank = 1;
    foreach ($team_performance as $team_name => $performance) {
        $output .= sprintf(
            '<tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>',
            htmlspecialchars($rank),
            htmlspecialchars($team_name),
            htmlspecialchars($performance['gold']),
            htmlspecialchars($performance['silver']),
            htmlspecialchars($performance['bronze']),
            htmlspecialchars($performance['total_score'])
        );
        $rank++;
    }
}

echo $output;