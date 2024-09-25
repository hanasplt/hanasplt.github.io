<?php

require_once '../../../config/sessionConfig.php';
$conn = require_once '../../../config/db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];

    $sql = "CALL sp_chckJudge(?,?)"; // This was for dropdown of teams to be judged
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $evid, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $judgedTeams = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tid = $row['teamId'];
            $judgedTeams[] = $tid;
        }
    }
    $result->free();
    $stmt->close();

    // Retrieve Contestants from this event
    $sql = "CALL sp_getEventContestant(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $teams = array(); // Initialize an array to store contestants
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $teams[] = $row; // Store contestants' info.
        }
    }
    $result->free();
    $stmt->close();

    // Retrieve Event Criteria
    $sql = "CALL sp_getCriteria(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to store event's criteria
    $eventCriterias = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventCriterias[] = $row; // Store criteria info.
        }
    }
    $result->free();
    $stmt->close();


    // Condition for displaying criteria or inform end-user whether no criteria
    if (!empty($teams) && !empty($eventCriterias)) {
        echo '<input type="text" name="evName" id="evName" value="'.$evid.'" hidden>';
        echo '<input type="text" name="action" value="record" hidden>';
        echo '<div class="ParticipantsCont">
                <table>
                    <tr>
                        <td>Contestant No.</td>';
                        $totalPercentage = 0;
                        foreach ($eventCriterias as $cri) { // Display Criteria
                            $totalPercentage += $cri['percentage'];
                            echo "<td>$cri[criteria] ($cri[percentage]%)</td>";
                        }
                echo '<td>Total ('. $totalPercentage .'%)</td>
                    </tr>';

                    foreach ($teams as $team) {
                        $teamCount = $team['contNo'];
                        echo '<input type="hidden" name="contestant" id="contestant" value="'.$team['contId'].'"/>';
                        echo '<tr>
                                <td>' . $teamCount++ . '</td>'; // Display Contestant No.
                                
                        foreach ($eventCriterias as $cri) { // Display Criteria Score input
                            echo '<td>
                                    <input class="criteriaInput'.$teamCount.'" type="number" 
                                    name="criteria[' . $cri['criteriaId'] . ']" 
                                    data-criteria="' . $cri['criteria'] . '" 
                                    max="' . $cri['percentage'] . '" 
                                    min="0" required onchange="calculateTotal('.$teamCount.')" />
                                </td>';
                        }
                        echo '<td>
                                <input type="number" name="totalScore'. $teamCount . '" 
                                id="totalScore'. $teamCount . '" readonly />
                              </td>'; // Display Calculated Total Score
                        echo '</tr>';
                    }

        echo '</table>';
        
        echo '</div>';
    } else {
        echo '<div class="ParticipantsCont">
                <table>
                    <tr><td colspan="3">No Criteria for Judging.</td></tr>
                </table>';
    }

    
    // When clicked 'Record Score', score summary with tally will be display
    echo '<div class="CRButton">
            <input type="button" class="GoButton" name="Back" value="BACK" onclick="window.location.href = \'judge.php?id='.$id.'\'" />
            <button class="ComputeB" type="submit">Record Score</button>
        </div>';
}
$conn->close();
?>