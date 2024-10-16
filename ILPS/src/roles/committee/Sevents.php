<?php

require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not
require_once 'committeePermissions.php'; // Retrieves committee permissions

$id = $_SESSION['userId'];


$evId = isset($_GET['event']) ? $_GET['event'] : '';
$evname = isset($_GET['name']) ? $_GET['name'] : '';
$contestantAId = isset($_GET['contestantAId']) ? $_GET['contestantAId'] : '';
$contestantBId = isset($_GET['contestantBId']) ? $_GET['contestantBId'] : '';

// Fetch team names
$query_teams = "SELECT teamId, teamName FROM teams";
$result_teams = $conn->query($query_teams);

$teams = [];
if ($result_teams->num_rows > 0) {
    while ($row_team = $result_teams->fetch_assoc()) {
        $teams[$row_team['teamId']] = $row_team['teamName'];
    }
}


// fetch scheduled days
$query_days = "SELECT * FROM scheduled_days";
$result_days = $conn->query($query_days);

$scheduled_days = [];
$schedCount = 0;

if ($result_days->num_rows > 0) {
    while ($row_day = $result_days->fetch_assoc()) {
        $day_id = $row_day['id'];

        // fetch events for each day
        $query_events = "SELECT * FROM scheduled_eventstoday WHERE day_id = ? AND activity = ? ORDER BY time ASC";
        $stmt_events = $conn->prepare($query_events);
        $stmt_events->bind_param("is", $day_id, $evname);
        $stmt_events->execute();
        $result_events = $stmt_events->get_result();

        $events = [];
        if ($result_events->num_rows > 0) {
            while ($row_event = $result_events->fetch_assoc()) {
                // fetch team names using the team IDs from the stored procedure result
                $teamA_id = $row_event['teamA'];
                $teamB_id = $row_event['teamB'];

                $teamA_name = isset($teams[$teamA_id]) ? $teams[$teamA_id] : '';
                $teamB_name = isset($teams[$teamB_id]) ? $teams[$teamB_id] : '';

                $row_event['teamA_name'] = $teamA_name;
                $row_event['teamB_name'] = $teamB_name;

                $events[] = $row_event;
                $schedCount++;
            }

            usort($events, function ($a, $b) {
                return strcmp($a['time'], $b['time']);
            });
        }

        $row_day['events'] = $events;
        $scheduled_days[] = $row_day;
    }
}

usort($scheduled_days, function ($a, $b) {
    return strtotime($a['day_date']) - strtotime($b['day_date']);
});

// UPDATE RESULT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventId = $_POST['eventId'];
    $teamA = $_POST['teamA'];
    $teamB = $_POST['teamB'];
    $teamAScore = $_POST['teamAScore'];
    $teamBScore = $_POST['teamBScore'];
    $contestantAId = $_POST['contestantAId']; 
    $contestantBId = $_POST['contestantBId'];
    $Status = 'Ended';

    // Update the scheduled_eventstoday table
    $query = "UPDATE scheduled_eventstoday SET status = ?, ResultA = ?, ResultB = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $Status, $teamA, $teamB, $eventId);

    
          
// Check if contestant IDs exist
$contestantCheckQuery = "SELECT teamId FROM contestant WHERE teamId IN (?, ?)";
$contestantCheckStmt = $conn->prepare($contestantCheckQuery);
$contestantCheckStmt->bind_param("ii", $contestantAId, $contestantBId);
$contestantCheckStmt->execute();
$contestantCheckResult = $contestantCheckStmt->get_result();

$existingContestants = [];
while ($row = $contestantCheckResult->fetch_assoc()) {
    $existingContestants[] = $row['teamId'];
}

// Verify contestant IDs
if (!in_array($contestantAId, $existingContestants) || !in_array($contestantBId, $existingContestants)) {
    echo json_encode(['success' => false, 'message' => 'One or more contestant IDs do not exist.']);
    exit;
}
    if ($stmt->execute()) {

        // Function to handle sub_results insert or update
        function insertOrUpdateSubResult($conn, $eventId, $contestantId, $id, $totalScore)
        {
  
            // Check if a record exists in sub_results
            $checkQuery = "SELECT * FROM sub_results WHERE eventId = ? AND contestantId = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("ii", $eventId, $contestantId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            
    
            if ($result->num_rows < 1) {
               

                // Record doesn't exist, insert a new one
                $insertQuery = "INSERT INTO sub_results (eventId, contestantId, personnelId, total_score)
            VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("iiid", $eventId, $contestantId, $id, $totalScore);
                if ($insertStmt->execute()) {
                    echo "Insert Successfully";
                }
            } else {
                 // Record exists, update the score
                 $updateQuery = "UPDATE sub_results SET total_score = ? WHERE eventId = ? AND contestantId = ?";
                 $updateStmt = $conn->prepare($updateQuery);
                 $updateStmt->bind_param("dii", $totalScore, $eventId, $contestantId);
                 if ($updateStmt->execute()) {
                    echo "Update Successfully";
                }
            }
    
            // Close the statements
            if (isset($checkStmt)) $checkStmt->close();
            if (isset($updateStmt)) $updateStmt->close();
            if (isset($insertStmt)) $insertStmt->close();
        }
    
        // Insert or update scores for team A and team B
        insertOrUpdateSubResult($conn, $eventId, $contestantAId, $id, $teamAScore);
        insertOrUpdateSubResult($conn, $eventId, $contestantBId, $id, $teamBScore);
    
        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'The result has been successfully saved.']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update data.']);
    }
    
    $stmt->close();
    
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../committee/css/Sevents.css">
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>

<body>

    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>COMMMITTEE</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.html';" hidden>Home</p>
            <p onclick="window.location.href = 'accounts.html';" hidden>Accounts</p>
            <p onclick="window.location.href = 'create-team.html';" hidden>Teams</p>
            <p onclick="window.location.href = 'EventTeam.html';" hidden>Events</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>

    <div class="sub-head" style="margin-top: 8%;">
        <button id="backbtn-faci" onclick="window.location.href='committee.php?id=<?php echo $id; ?>'">
            <img src="../../../public/assets/icons/back.png" alt="back arrow button" width="20" style="margin-right: 5px;">
            Back
        </button>
        <h1 style="text-align: center;"><?php echo $evname; ?></h1>
        <?php
        if (isset($_SESSION['error'])) { // For displaying error
            echo '
                <div class="msg" id="msg-container">
                    <div class="msg-content">
                        <span style="display: flex; align-items: center; justify-content: space-around;">
                            <p id="error-msg">' . $_SESSION['error'] . '</p>
                            <button type="button" id="x-btn">X</button>
                        </span>
                    </div>
                </div>
                ';
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <?php // Display table - permitted to view
    if (in_array('committee_scoring_read', $comt_rights)) { ?>
        <!-- NEW INTERFACE -->
        <div class="recordTable">
            <table style="margin: auto">
                <tr>
                    <td>TIME</td>
                    <td>EVENT</td>
                    <td>GAME NO.</td>
                    <td>TEAM A</td>
                    <td>POINTS</td>
                    <td>TEAM B</td>
                    <td>POINTS</td>
                    <td>ACTION</td>
                    <td hidden>
                        <input type="text" class="event-status" value="<?php echo htmlspecialchars($event['status']); ?>" />
                    </td>
                </tr>
                <?php
                if ($schedCount == 0) { // Display message - no scheduled event(s)
                    echo '
                <tr>
                    <td colspan="9" style="text-align: center; color: red;">No Schedule.</td>
                </tr>
                ';
                }
                ?>
                <?php foreach ($scheduled_days as $day): ?>
                    <?php foreach ($day['events'] as $event): ?>
                        <tr>
                            <td><?php echo date('m/d/Y h:i A', strtotime($event['time'])); ?></td>
                            <td><?php echo htmlspecialchars($event['activity']); ?></td>
                            <td><?php echo htmlspecialchars($event['gameNo']); ?></td>
                            <td>
                                <select name="teamA" class="teamA non-editable" id="teamA-<?php echo htmlspecialchars($event['gameNo']); ?>" onchange="syncTeams(this, '<?php echo htmlspecialchars($event['gameNo']); ?>'); changeColor(this)" disabled>
                                    <option value="">(<?php echo $event['teamA_name']; ?>) No Result</option>
                                    <option value="Winner" <?php echo $event['ResultA'] == 'Winner' ? 'selected' : ''; ?>>(<?php echo $event['teamA_name']; ?>) Winner</option>
                                    <option value="Loser" <?php echo $event['ResultA'] == 'Loser' ? 'selected' : ''; ?>>(<?php echo $event['teamA_name']; ?>) Loser</option>
                                </select>
                            </td>
                            <td>
    <?php
    // Prepare and bind for sub_results
    $contestantAId = $event['teamA']; // Assuming this is the contestant ID
    echo "<input type='hidden' class='contestantAId' name='contestantAId' value='" . $contestantAId . "'>";
    $stmt = $conn->prepare("SELECT * FROM sub_results WHERE eventId = ? AND contestantId = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("ii", $evId, $contestantAId); // Assuming both are integers

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate the select options
    echo "<select class='teamAScore non-editable' name='teamAScore' disabled>";
    $selectedPoints = 0; // Default to 0 in case no result is found

    // Fetch the points from sub_results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $selectedPoints = $row['total_score'];
            // Include contestant ID in the option value (optional)
            echo "<option value='" . $row['total_score'] . "' " . ($selectedPoints == $row['total_score'] ? 'selected' : '') . ">" . $row['total_score'] . "</option>";
        }
    }

    // Always include the default 0 option if no result is found
    echo "<option value='0' " . ($selectedPoints == 0 ? 'selected' : '') . ">0</option>";

    // Close the statement for sub_results
    $stmt->close();

    // Fetch all data from vw_eventscore (no WHERE condition)
    $query_vw = "SELECT * FROM vw_eventscore"; // No condition here
    $stmt_vw = $conn->prepare($query_vw);
    if (!$stmt_vw) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Execute the statement for vw_eventscore
    $stmt_vw->execute();
    $result_vw = $stmt_vw->get_result();

    // Fetch the points from vw_eventscore and add them to the select dropdown
    if ($result_vw->num_rows > 0) {
        while ($row_vw = $result_vw->fetch_assoc()) {
            $score = $row_vw['points'];
            echo "<option value='" . $score . "' " . ($selectedPoints == $score ? 'selected' : '') . ">" . $score . "</option>";
        }
    }

    // Close the statement for vw_eventscore
    $stmt_vw->close();

    echo "</select>";
    ?>
</td>

                            <td>
                                <select name="teamB" class="teamB non-editable" id="teamB-<?php echo htmlspecialchars($event['gameNo']); ?>" onchange="syncTeams(this, '<?php echo htmlspecialchars($event['gameNo']); ?>'); changeColor(this)" disabled>
                                    <option value="">(<?php echo $event['teamB_name']; ?>) No Result</option>
                                    <option value="Winner" <?php echo $event['ResultB'] == 'Winner' ? 'selected' : ''; ?>>(<?php echo $event['teamB_name']; ?>) Winner</option>
                                    <option value="Loser" <?php echo $event['ResultB'] == 'Loser' ? 'selected' : ''; ?>>(<?php echo $event['teamB_name']; ?>) Loser</option>
                                </select>
                            </td>
                            <td>
    <?php
    // Prepare and bind for sub_results
    $contestantBId = $event['teamB']; // Assuming this is the contestant ID
    echo "<input type='hidden' class='contestantBId' name='contestantBId' value='" . $contestantBId . "'>";
    $stmt = $conn->prepare("SELECT * FROM sub_results WHERE eventId = ? AND contestantId = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("ii", $evId, $contestantBId); // Assuming both are integers

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate the select options
    echo "<select class='teamBScore non-editable' name='teamBScore' disabled>";
    $selectedPoints = 0; // Default to 0 in case no result is found

    // Fetch the points from sub_results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $selectedPoints = $row['total_score'];
            // Include contestant ID in the option value (optional)
            echo "<option value='" . $row['total_score'] . "' " . ($selectedPoints == $row['total_score'] ? 'selected' : '') . ">" . $row['total_score'] . "</option>";
        }
    }

    // Always include the default 0 option if no result is found
    echo "<option value='0' " . ($selectedPoints == 0 ? 'selected' : '') . ">0</option>";

    // Close the statement for sub_results
    $stmt->close();

    // Fetch all data from vw_eventscore (no WHERE condition)
    $query_vw = "SELECT * FROM vw_eventscore"; // No condition here
    $stmt_vw = $conn->prepare($query_vw);
    if (!$stmt_vw) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Execute the statement for vw_eventscore
    $stmt_vw->execute();
    $result_vw = $stmt_vw->get_result();

    // Fetch the points from vw_eventscore and add them to the select dropdown
    if ($result_vw->num_rows > 0) {
        while ($row_vw = $result_vw->fetch_assoc()) {
            $score = $row_vw['points'];
            echo "<option value='" . $score . "' " . ($selectedPoints == $score ? 'selected' : '') . ">" . $score . "</option>";
        }
    }

    // Close the statement for vw_eventscore
    $stmt_vw->close();

    echo "</select>";
    ?>
</td>

                            <td>
                                <?php // Display Edit button - permitted to update
                                if (in_array('committee_scoring_update', $comt_rights)) { ?>
                                    <button class="edit-btn" data-event-id="<?php echo $event['id']; ?>">Edit</button>
                                <?php } else {
                                    echo '
                                    <p style="color: darkgrey;">Feature denied.</p>
                                ';
                                }
                                // Display Save button - permitted to add
                                if (in_array('committee_scoring_add', $comt_rights)) { ?>
                                    <button id="save-btn" class="save-btn" style="display: none;" data-event-id="<?php echo $event['id']; ?>">Save</button>
                                <?php } ?>
                                <button class="cancel-btn" style="display: none;" data-event-id="<?php echo $event['id']; ?>">Cancel</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </table>
        </div>
        <!-- SYNC TEAM RESULT -->
        <script>
            function syncTeams(changedSelect, gameNo) {
                const teamASelect = document.querySelector(`#teamA-${gameNo}`);
                const teamBSelect = document.querySelector(`#teamB-${gameNo}`);

                // Check which dropdown was changed
                if (teamASelect === changedSelect) {
                    // If team A is set to 'Winner', automatically set team B to 'Loser'
                    if (teamASelect.value === 'Winner') {
                        teamBSelect.value = 'Loser';
                        teamBSelect.style.color = 'red';
                        teamASelect.style.color = 'green';
                    } else if (teamASelect.value === 'Loser') {
                        teamBSelect.value = 'Winner';
                        teamBSelect.style.color = 'green';
                        teamASelect.style.color = 'red';
                    }
                } else if (teamBSelect === changedSelect) {
                    // Similarly for team B
                    if (teamBSelect.value === 'Winner') {
                        teamASelect.value = 'Loser';
                        teamASelect.style.color = 'red';
                        teamBSelect.style.color = 'green';
                    } else if (teamBSelect.value === 'Loser') {
                        teamASelect.value = 'Winner';
                        teamASelect.style.color = 'green';
                        teamBSelect.style.color = 'red';
                    }
                }
            }

            // RESULT COLOR
            function changeColor(selectElement) {
                if (selectElement.value === 'Winner') {
                    selectElement.style.color = 'green'; // Change to green if Winner
                } else if (selectElement.value === 'Loser') {
                    selectElement.style.color = 'red'; // Change to red if Loser
                } else {
                    selectElement.style.color = ''; // Default color for no selection
                }
            }

            // Call changeColor on page load to set initial color
            document.querySelectorAll('.teamB').forEach(select => {
                changeColor(select);
            });

            document.querySelectorAll('.teamA').forEach(select => {
                changeColor(select);
            });
        </script>


        <!-- UPDATE RESULT -->
        <script>
            // Function to enable editing and show the Save button
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');

                    // Enable the dropdowns in the current row
                    row.querySelectorAll('select').forEach(select => {
                        select.disabled = false;
                    });

                    var savebtn = document.getElementById('save-btn');

                    // Hide the Edit button and show the Save button again
                    row.querySelector('.edit-btn').style.display = 'none';
                    if (savebtn) { // Ensures save button exists
                        row.querySelector('.save-btn').style.display = 'inline-block';
                    }
                    row.querySelector('.cancel-btn').style.display = 'inline-block';

                    this.disabled = false; // Disable the Edit button
                });
            });

            // Function to enable editing and show the cancel button
            document.querySelectorAll('.cancel-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');

                    // Enable the dropdowns in the current row
                    row.querySelectorAll('select').forEach(select => {
                        select.disabled = true; // Disable dropdowns again
                    });

                    var savebtn = document.getElementById('save-btn');

                    // Show the Edit button and hide the Save and Cancel buttons
                    row.querySelector('.edit-btn').style.display = 'inline-block';
                    if (savebtn) { // Ensures save button exists
                        row.querySelector('.save-btn').style.display = 'none';
                    }
                    row.querySelector('.cancel-btn').style.display = 'none';

                    this.disabled = false; // Keep the cancel button enabled
                });
            });


           // Function to save the changes when Save button is clicked
           document.querySelectorAll('.save-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        row.querySelector('.cancel-btn').style.display = 'none';

        // Get data from the row
        const eventId = this.getAttribute('data-event-id');
        const teamA = row.querySelector('.teamA').value;
        const teamB = row.querySelector('.teamB').value;

        // Updated to correctly fetch the selected score for teamA and teamB
        const teamAScore = row.querySelector('.teamAScore').value; // Fetch the score for teamA
        const teamBScore = row.querySelector('.teamBScore').value; // Fetch the score for teamB

        // Fetch the contestant IDs directly from the row
        const contestantAIdInput = row.querySelector('.contestantAId');
        const contestantBIdInput = row.querySelector('.contestantBId');

        // Safely access their values
        const contestantAId = contestantAIdInput ? contestantAIdInput.value : null; // Safe access
        const contestantBId = contestantBIdInput ? contestantBIdInput.value : null; // Safe access

        // Log the data being sent
        console.log('Sending data:');
        console.log('eventId:', eventId);
        console.log('teamA:', teamA);
        console.log('teamB:', teamB);
        console.log('teamAScore:', teamAScore);
        console.log('teamBScore:', teamBScore);
        console.log("Contestant A ID:", contestantAId);
        console.log("Contestant B ID:", contestantBId);

        // AJAX to update data
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'Sevents.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Success response
                console.log('Response received:', xhr.responseText); // Log the server response
                Swal.fire({
                    title: 'Success!',
                    text: 'Data has been updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // Disable dropdowns after saving
                row.querySelectorAll('select').forEach(select => {
                    select.disabled = true;
                });

                // Show the Edit button and hide the Save button
                row.querySelector('.edit-btn').style.display = 'inline-block';
                row.querySelector('.save-btn').style.display = 'none';
            } else {
                console.error('Error response:', xhr.status, xhr.statusText); // Log the error response
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        };

        // Send the AJAX request with the data
        xhr.send(`eventId=${eventId}&teamA=${teamA}&teamB=${teamB}&teamAScore=${teamAScore}&teamBScore=${teamBScore}&contestantAId=${contestantAId}&contestantBId=${contestantBId}`);
    });
});


        </script>

    <?php } else { // Display message - not permitted to view
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Scoring Table.
            </div>
        ';
    } ?>

</body>

</html>