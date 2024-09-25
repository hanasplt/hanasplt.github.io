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
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $events[] = array('event' => $row['eventID']);
        }
    }
    $result->free();
    $stmt->close();

    $output = '<tr>
                    <th class="rank-column">Rank</th>
                    <th class="name-column">Team Name</th>
                    <th class="gold-column"><img src="../../../public/assets/icons/gold-medal.png" class="medal-icon"></th>
                    <th class="silver-column"><img src="../../../public/assets/icons/silver-medal.png" class="medal-icon"></th>
                    <th class="bronze-column"><img src="../../../public/assets/icons/bronze-medal.png" class="medal-icon"></th>
                    <th class="points-column">Total Points</th>
                </tr>';

    //fetch all contestants ranking in every events
    $teams = array();
    foreach($events as $ev) {
        $evid = $ev['event'];

        $sql = "CALL sp_getTeamData(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $evid);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $teams[] = array('evid' => $row['eventId'], 'conid' => $row['contestantId'], 'team' => $row['team'],
                                'rank' => $row['rank']);
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
    foreach($teams as $team) {
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
   

    //display overall ranking
    $display = "CALL sp_getLead()";
    $stmt = $conn->prepare($display);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output .= '<tr>
                        <td>' . $row['rank'] . '</td>
                        <td><img src="../../../public/assets/icons/sample.png"> ' . $row['team'] . '</td>
                        <td>' . $row['pts'] . '</td>
                    </tr>';
        }
    }

    echo $output;

?>