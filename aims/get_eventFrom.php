// get_eventFrom.php

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ilps";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$id = $_SESSION['judgeId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];

    $sql = "CALL sp_chckJudge(?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $evid, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $judgedTeams = array();
    echo '<input type="text" name="evName" id="evName" value="'.$evid.'" hidden>';
    echo '<input type="text" name="action" value="record" hidden>';
    echo '<div class="ParticipantsCont">
            <select class="Participantss" name="nameSelect" id="nameSelect" required>';
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tid = $row['teamId'];
            $judgedTeams[] = $tid;
        }
    }
    $result->free();
    $stmt->close();
    
    $sql = "CALL sp_getEventContestant(?)";
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
    
    foreach ($teams as $team) {
        if (!in_array($team['teamId'], $judgedTeams)) {
            echo '<option value="'.$team['teamId'].'">'.$team['teamName'].'</option>';
        }
    }
    
    echo '</select>
        </div>';
    

    $sql = "CALL sp_getCriteria(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="participantsrecord">
            <table border="1" style="border-radius: 5px;">
                <tr>
                    <th>Criteria For Judging</th>
                    <th>Percent</th>
                    <th>Scores</th>
                </tr>';
    $per = array();
    if ($result->num_rows > 0) {
        $totalpts = 0;
        while ($row = $result->fetch_assoc()) {
            $totalpts += $row['percentage'];
            echo '<tr>';
            echo '<td>' . $row['criteria'] . '</td>';
            echo '<td style="text-align: center;">' . $row['percentage'] . '</td>';
            echo '<td><input class="criteriaInput" type="number" name="criteria[' . $row['criteriaId'] . ']" data-criteria="' . $row['criteria'] . '" max="' . $row['percentage'] . '" min="0" required onchange="calculateTotal()" /></td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<th>TOTAL</td>';
        echo '<td style="text-align: center;">' . $totalpts . '</td>';
        echo '<td><input type="number" name="totalScore" id="totalScore" readonly /></td>';
        echo '</tr>';
    } else {
        echo '<tr><td colspan="3">No Criteria for Judging.</td></tr>';
    }
    echo '</table>
        </div>';
    
    echo '<div class="CRButton">
            <input type="button" class="GoButton" name="Back" value="BACK" onclick="window.location.href = \'judge.php?id='.$id.'\'" />
            <button class="ComputeB" type="submit">Record Score</button>
        </div>';

    $result->free();
    $stmt->close();
}
$conn->close();
?>