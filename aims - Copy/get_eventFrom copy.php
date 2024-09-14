<?php
$conn = include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$id = $_SESSION['judgeId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];

    $sql = "CALL sp_chckJudge(?,?)"; // I dont know
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

    $sql = "CALL sp_getEventContestant(?)"; // Retrieve Contestants from this event
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $teams = array(); 
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $teams[] = $row;
        }
    }
    $result->free();
    $stmt->close();


    $sql = "CALL sp_getCriteria(?)"; // Retrieve Event Criteria
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    $eventCriterias = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventCriterias[] = $row;
        }
    }
    $result->free();
    $stmt->close();


    // Condition for displaying criteria or informa end-user whether no criteria is added
    if (!empty($teams) && !empty($eventCriterias)) {
        echo '<input type="text" name="evName" id="evName" value="'.$evid.'" hidden>';
        echo '<input type="text" name="action" value="record" hidden>';
        echo '<div class="ParticipantsCont">
                <table>
                    <tr>
                        <td>Contestant No.</td>';
                        $totalPercentage = 0;
                        foreach ($eventCriterias as $cri) {
                            $totalPercentage += $cri['percentage'];
                            echo "<td>$cri[criteria] ($cri[percentage]%)</td>";
                        }
                echo '<td>Total ('. $totalPercentage .'%)</td>
                    </tr>';

                    // Display Contestant Number (dummy cont no.)
                    $teamCount = 1;

                    foreach ($teams as $team) {
                        echo '<tr>
                                <td>' . $teamCount++ . '</td>';
                                
                        foreach ($eventCriterias as $cri) {
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
                              </td>';
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