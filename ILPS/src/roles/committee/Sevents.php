<?php

require_once '../../../config/sessionConfig.php'; // Session Cookie
$conn = require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['userId'];


$evId = isset($_GET['event']) ? $_GET['event'] : '';
$evname = isset($_GET['name']) ? $_GET['name'] : '';

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
    $Status = 'Ended';

    // You would update your database here based on the event and teams
    $query = "UPDATE scheduled_eventstoday SET status = ?, ResultA = ?, ResultB = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $Status, $teamA, $teamB, $eventId);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'The result has been successfully saved.']);
    } else {
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
            <?php foreach ($scheduled_days as $day): ?>
                <?php foreach ($day['events'] as $event): ?>
                    <tr>
                        <td><?php echo date('m/d/Y h:i A', strtotime($event['time'])); ?></td>
                        <td><?php echo htmlspecialchars($event['activity']); ?></td>
                        <td><?php echo htmlspecialchars($event['gameNo']); ?></td>
                        <td>
                            <select class="teamA non-editable" id="teamA-<?php echo htmlspecialchars($event['gameNo']); ?>" onchange="syncTeams(this, '<?php echo htmlspecialchars($event['gameNo']); ?>'); changeColor(this)" disabled>
                                <option value="">(<?php echo $event['teamA_name']; ?>) No Result</option>
                                <option value="Winner" <?php echo $event['ResultA'] == 'Winner' ? 'selected' : ''; ?>>(<?php echo $event['teamA_name']; ?>) Winner</option>
                                <option value="Loser" <?php echo $event['ResultA'] == 'Loser' ? 'selected' : ''; ?>>(<?php echo $event['teamA_name']; ?>) Loser</option>
                            </select>
                        </td>
                        <input type="hidden" name="contestantA_id" value="<?php echo $teamA_id; ?>"> <!-- Add hidden input for contestant ID -->
                        <td>SCORE</td>
                        <td>
                            <select class="teamB non-editable" id="teamB-<?php echo htmlspecialchars($event['gameNo']); ?>" onchange="syncTeams(this, '<?php echo htmlspecialchars($event['gameNo']); ?>'); changeColor(this)" disabled>
                                <option value="">(<?php echo $event['teamB_name']; ?>) No Result</option>
                                <option value="Winner" <?php echo $event['ResultB'] == 'Winner' ? 'selected' : ''; ?>>(<?php echo $event['teamB_name']; ?>) Winner</option>
                                <option value="Loser" <?php echo $event['ResultB'] == 'Loser' ? 'selected' : ''; ?>>(<?php echo $event['teamB_name']; ?>) Loser</option>
                            </select>
                        </td>
                        <td>SCORE</td>
                        <td>
                            <button class="edit-btn" data-event-id="<?php echo $event['id']; ?>">Edit</button>
                            <button class="save-btn" style="display: none;" data-event-id="<?php echo $event['id']; ?>">Save</button>
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

                // Hide the Edit button and show the Save button again
                row.querySelector('.edit-btn').style.display = 'none';
                row.querySelector('.save-btn').style.display = 'inline-block';
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

                // Show the Edit button and hide the Save and Cancel buttons
                row.querySelector('.edit-btn').style.display = 'inline-block';
                row.querySelector('.save-btn').style.display = 'none';
                row.querySelector('.cancel-btn').style.display = 'none';

                this.disabled = false; // Keep the cancel button enabled
            });
        });


        // Function to save the changes when Save button is clicked
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');

                // Get data from the row
                const eventId = this.getAttribute('data-event-id');
                const teamA = row.querySelector('.teamA').value;
                const teamB = row.querySelector('.teamB').value;

                // AJAX to update data
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'Sevents.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Success response
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
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                };

                xhr.send(`eventId=${eventId}&teamA=${teamA}&teamB=${teamB}`);
            });
        });
    </script>


</body>

</html>