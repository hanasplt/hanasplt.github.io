<?php

require_once '../../../config/sessionConfig.php';
$conn = require_once '../../../config/db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];
    $evname = $_POST['evname'];
/*
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
*/
    
    // Verify if judge is done scoring
    $checkEventScore = "SELECT vp.contNo as Contestant_No, vs.*,
                                DENSE_RANK() OVER (ORDER BY vs.total_score DESC) AS rank
                        FROM vw_subresult vs
                        INNER JOIN vw_eventParti vp ON vs.contestantId = vp.contId
                        WHERE vs.eventId = ? AND vs.personnelId = ?;";
    
    $stmt = $conn->prepare($checkEventScore);
    $stmt->bind_param("ii", $evid, $id);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) { // Done scoring
        // DISPLAY SCORESHEET
        echo 
        '<div class="tally-container" id="TallyTable">
            <table id="tallyTable">
                <tr id="tabletr">
                    <td id="headCol">Contest No.</td>'; // Contestant Numbers
                        $get = "CALL sp_getCriteria(?)"; // Retrieve Criteria for this event
                        $prepstmt = $conn->prepare($get);
                        $prepstmt->bind_param("i", $evid);
                        $prepstmt->execute();
                        $retCri = $prepstmt->get_result();

                        $criCount = 0;
                        if ($retCri->num_rows > 0) {
                            while($row = $retCri->fetch_assoc()) {
                                $criCount++;
                                $cri = $row['criteria'];
                                // Display Criterias as Row Header
                                ?>
                                <td id="headCol"><?php echo $cri; ?></td>
                                <?php
                            }
                        }
                        $retCri->free();
                        $prepstmt->close();

                        // Display Total Score and Ranking
                    echo '<td id="headCol">Total Score</td>
                        <td id="headCol">Rank</td>
                </tr>';
                        while ($row = $retval->fetch_assoc()) {
                            // Display Scoresheet Data
                            $contnum = $row['Contestant_No'];
                            $tscore = $row['total_score'];
                            $rank = $row['rank'];

                            ?>
                            <tr style="text-align: left;" id="tabletr">
                                <td><?php echo $contnum; ?></td>
                            <?php
                            // Criteria Score
                            for($x = 1; $x <= $criCount; $x++) {
                                ?>
                                <td id="scoreCol"><?php echo $row['criteria'.$x]; ?></td>
                                <?php
                            }
                            ?>
                                <td><?php echo $tscore; ?></td>
                                <td><?php echo $rank; ?></td>
                            </tr>
                            <?php
                        }
                        
                        $retval->free();
                        $stmt->close();
            echo '</table>
        </div>

        <div class="facilitators-container">';
                $sql = "CALL sp_getJudge(?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $id, $evid);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                    while($row = $retval->fetch_assoc()) {
                        $judge = $row['firstName'];
                        ?>
                        <div class="col">
                            <b><?php echo $judge ?></b><br>
                            Event Judge
                        </div>
                        <?php
                    }
                }
                $retval->free();
                $stmt->close();

        echo '<input type="button" class="GoButton" name="Back" value="BACK" onclick="window.location.href = \'judge.php?id='.$id.'\'" />
        </div>';
    // end scoresheet
    } else { // Haven't judge yet
        $retval->free();
        $stmt->close();

        // DISPLAY THE JUDGE SCORESHEET

        $sql = "CALL sp_getEventContestant(?)"; // Retrieve Contestants from this event
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
        if (!empty($teams) && !empty($eventCriterias)) { // Team and Criteria is available
            echo '<input type="text" name="evID" id="evID" value="'.$evid.'" hidden>';
            echo '<input type="text" name="evname" id="evname" value="'.$evname.'" hidden>';
            echo '<input type="int" name="user_ID" id="user_ID" value="'.$id.'" hidden>';
            echo '<div class="ParticipantsCont">
                    <table>
                        <tr>
                            <td>Contestant No.</td>';

                            $totalPercentage = 0; // Calculate total score of the Criteria
                            foreach ($eventCriterias as $cri) { // Display Header Row (Criterias)
                                $totalPercentage += $cri['percentage'];
                                echo "<td>$cri[criteria] ($cri[percentage]%)</td>";
                            } // end for each loop for criterias

                    echo '<td>Total ('. $totalPercentage .'%)</td>
                        </tr>'; // Criteria Percentage

                        foreach ($teams as $team) {
                            $contestantId = $team['contId'];
                            $teamCount = $team['contNo'];
                            
                            echo '<tr>
                                    <td>' . $teamCount . '</td>'; // Display Contestant No.
                                    
                            foreach ($eventCriterias as $cri) { // Display Criteria Score input
                                // Score input fields
                                echo '<td>
                                        <input class="criteriaInput'.$contestantId.'" type="number" 
                                        name="criteria['. $contestantId .'][' . $cri['criteriaId'] . ']" 
                                        data-criteria="' . $cri['criteria'] . '" 
                                        max="' . $cri['percentage'] . '" 
                                        min="0" required onchange="calculateTotal('.$contestantId.')" />
                                    </td>';
                            } #end for each loop

                            // Total Score input field for display
                            echo '<td>
                                    <input type="number" name="totalScore'. $contestantId . '" 
                                    id="totalScore'. $contestantId . '" readonly />
                                </td>'; // Display Calculated Total Score
                            echo '</tr>';

                            // Retrievable contestant IDs for submission
                            echo '<input type="hidden" name="contestant[]" value="'. $contestantId .'" />';
                        }

            echo '</table>';
            
            echo '</div>';

            // When clicked 'Record Score', score summary with tally will be display
            echo '<div class="CRButton">
                    <input type="button" class="GoButton" name="Back" value="BACK" onclick="window.location.href = \'judge.php?id='.$id.'\'" />
                    <button class="ComputeB" type="submit">Record Score</button>
                </div>';
        } else {
            echo '
            <div class="ParticipantsCont">
                <table>
                    <tr><td colspan="3">No Criteria for Judging.</td></tr>
                </table>
            </div>
            <div class="CRButton">
                <input type="button" class="GoButton" name="Back" value="BACK" onclick="window.location.href = \'judge.php?id='.$id.'\'" />
            </div>';
        }
    }

}
$conn->close();
?>