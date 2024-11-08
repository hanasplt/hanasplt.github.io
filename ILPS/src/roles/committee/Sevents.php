<?php

require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not
require_once 'committeePermissions.php'; // Retrieves committee permissions

$id = $_SESSION['userId'];
$comm_name = $row['firstName'];

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
$schedCount = 0;

if ($result_days->num_rows > 0) {
    while ($row_day = $result_days->fetch_assoc()) {
        $day_id = $row_day['id'];
        $day_date = $row_day['day_date']; // Get the date for the day

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

                // Format the combined date and time
                $event_datetime = $day_date . ' ' . $row_event['time'];
                $row_event['formatted_datetime'] = date('m/d/Y h:i A', strtotime($event_datetime));

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

// Update Results
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventId = $_POST['eventId'];
    $teamA = $_POST['teamA'];
    $teamB = $_POST['teamB'];
    $Status = 'Ended';

    // Update the scheduled_eventstoday table for results
    $query = "UPDATE scheduled_eventstoday SET status = ?, ResultA = ?, ResultB = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $Status, $teamA, $teamB, $eventId);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Results updated successfully."]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS | Committee</title>
    <link rel="stylesheet" href="../committee/css/Sevents.css">
    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
        </nav>
        <nav class="nav-link-1">
            <div class="dropdown">
                <button class="dropbtn">
                    <img class="icon-img" src="../../../public/assets/icons/icon-user.jpg">
                    <div>
                        <p class="user-name"><?php echo $comm_name; ?></p>
                        <p class="comm-name">COMMITTEE</p>
                    </div>
                </button>
                <div class="dropdown-content">
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <div class="sub-head">
            <button id="backbtn-faci" onclick="window.location.href='committee.php?id=<?php echo $id; ?>'">
                <img src="../../../public/assets/icons/back.png" alt="back arrow button" width="20">
                Back
            </button>
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
        <div class="eventNameTitle">
            <h1><?php echo $evname; ?></h1>
        </div>
    </div>
    <?php
    // Assuming $evId is set to the current event ID
    $stmt_event = $conn->prepare("SELECT gameNo FROM scheduled_eventstoday WHERE activity = ?");
    $stmt_event->bind_param("s", $evname);
    $stmt_event->execute();
    $result_event = $stmt_event->get_result();

    if ($result_event->num_rows > 0) {
        $event_row = $result_event->fetch_assoc();
        if ($event_row['gameNo'] != 0 || $event_row['gameNo'] != null) {
            // Display table - permitted to view
            if (in_array('committee_scoring_read', $comt_rights)) { ?>
                <!-- NEW INTERFACE -->
                <div class="recordTable">
                    <table id="eventTable">
                        <thead>
                            <tr>
                                <td>TIME</td>
                                <td>EVENT</td>
                                <td>GAME NO.</td>
                                <td>TEAM A</td>
                                <td>TEAM B</td>
                                <td>ACTION</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($schedCount == 0) { // Display message - no scheduled event(s)
                                echo '
                        <tr>
                            <td colspan="6" style="text-align: center; color: red;">No Schedule.</td>
                        </tr>
                        ';
                            } else {
                                foreach ($scheduled_days as $day): ?>
                                    <?php foreach ($day['events'] as $event): ?>
                                        <tr class="event-row">
                                            <td><?php echo $event['formatted_datetime']; ?></td>
                                            <td><?php echo htmlspecialchars($event['activity']); ?></td>
                                            <td><?php echo htmlspecialchars($event['gameNo']); ?></td>
                                            <td>
                                                <select name="teamA" class="teamA non-editable" id="teamA-<?php echo htmlspecialchars($event['gameNo']); ?>" onchange="syncTeams(this, '<?php echo htmlspecialchars($event['gameNo']); ?>'); changeColor(this)" disabled>
                                                    <option value=""><?php echo $event['teamA_name']; ?> - No Result</option>
                                                    <option value="Winner" <?php echo $event['ResultA'] == 'Winner' ? 'selected' : ''; ?>><?php echo $event['teamA_name']; ?> - Winner</option>
                                                    <option value="Loser" <?php echo $event['ResultA'] == 'Loser' ? 'selected' : ''; ?>><?php echo $event['teamA_name']; ?> - Loser</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="teamB" class="teamB non-editable" id="teamB-<?php echo htmlspecialchars($event['gameNo']); ?>" onchange="syncTeams(this, '<?php echo htmlspecialchars($event['gameNo']); ?>'); changeColor(this)" disabled>
                                                    <option value=""><?php echo $event['teamB_name']; ?> - No Result</option>
                                                    <option value="Winner" <?php echo $event['ResultB'] == 'Winner' ? 'selected' : ''; ?>><?php echo $event['teamB_name']; ?> - Winner</option>
                                                    <option value="Loser" <?php echo $event['ResultB'] == 'Loser' ? 'selected' : ''; ?>><?php echo $event['teamB_name']; ?> - Loser</option>
                                                </select>
                                            </td>
                                            <td>
                                                <?php // Display Edit button - permitted to update
                                                if (in_array('committee_scoring_update', $comt_rights)) { ?>
                                                    <button class="edit-btn" data-event-id="<?php echo $event['id']; ?>">Edit</button>
                                                <?php } else {
                                                    echo '<p style="color: darkgrey;">Feature denied.</p>';
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
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div id="paginationControls" style="text-align: center; margin-top: 10px;">
                    <div id="pageNumbers"></div>
                </div>
                <script>
                    let currentPage = 1;
                    const rowsPerPage = 5;
                    const rows = document.querySelectorAll('.event-row');
                    const totalPages = Math.ceil(rows.length / rowsPerPage);

                    function displayRows() {
                        const start = (currentPage - 1) * rowsPerPage;
                        const end = start + rowsPerPage;

                        rows.forEach((row, index) => {
                            row.style.display = (index >= start && index < end) ? '' : 'none';
                        });
                    }

                    function updatePageNumbers() {
                        const pageNumbersContainer = document.getElementById('pageNumbers');
                        pageNumbersContainer.innerHTML = '';

                        for (let i = 1; i <= totalPages; i++) {
                            const pageButton = document.createElement('button');
                            pageButton.innerText = i;
                            pageButton.className = 'page-button';
                            pageButton.onclick = function() {
                                currentPage = i;
                                displayRows();
                                updatePageNumbers(); // Update the page numbers after changing the page
                            };

                            if (i === currentPage) {
                                pageButton.style.fontWeight = 'bold'; // Highlight current page
                            }

                            pageNumbersContainer.appendChild(pageButton);
                        }
                    }

                    // Initial display of rows and page numbers
                    displayRows();
                    updatePageNumbers();
                </script>
    <?php
            } else {
                echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Scoring Table.
            </div>
        ';
            }
        } else {
            // Optionally handle the case where the event type is not "Others"
        }
    } else {
        echo '<p style="text-align: center;">Event Schedule Not Found.</p>';
    }

    $stmt_event->close();
    ?>

    <!-- ADD or EDIT SCORES -->
    <div class="scoringContainer">
        <h1>Ranking and Scoring</h1>
        <div class="scoreTable">
            <table style="margin: auto;">
                <thead>
                    <td>TEAM</td>
                    <td>SCORE</td>
                </thead>

                <?php
                foreach ($teams as $teamId => $teamName) {
                    // Query to get contestants for each team for the selected event ID
                    $sql = "SELECT contId, teamId FROM contestant WHERE eventId = ? AND teamId = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $evId, $teamId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result === false) {
                        echo "<tr><td colspan='2'>Error fetching teams: " . htmlspecialchars($conn->error) . "</td></tr>";
                        continue;
                    }

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<input type='hidden' name='Teams[]' class='Teams' value='" . $teamId . "'>";
                            echo "<tr class='teamRow'>";
                            echo "<td class='teamNames'>" . htmlspecialchars($teamName) . "</td>";

                            echo "<td class='teamScores'>";
                            $contestantsId = $row['contId'];
                            echo "<input type='hidden' class='contestantId' name='contestantId[]' value='" . $contestantsId . "'>";

                            // Fetch total score for the current contestant
                            $stmt_sub = $conn->prepare("SELECT total_score FROM sub_results WHERE eventId = ? AND contestantId = ?");
                            $stmt_sub->bind_param("ii", $evId, $contestantsId);
                            $stmt_sub->execute();
                            $result_sub = $stmt_sub->get_result();

                            echo "<select class='teamsScore non-editable' name='teamsScore[]' onchange='updateScores(this)' disabled>";
                            $selectedPoints = 0;

                            if ($result_sub->num_rows > 0) {
                                while ($row_sub = $result_sub->fetch_assoc()) {
                                    $selectedPoints = $row_sub['total_score'];
                                }
                            }
                            echo "<option value='0' " . ($selectedPoints == 0 ? 'selected' : '') . ">0</option>";

                            // Fetch event scores and rankings for the specified event ID category
                            $query_vw = "SELECT points, rank FROM vw_eventscore WHERE eventCategory = 
                         (SELECT eventCategory FROM vw_events WHERE eventID = ?)";
                            $stmt_vw = $conn->prepare($query_vw);
                            $stmt_vw->bind_param("i", $evId);
                            $stmt_vw->execute();
                            $result_vw = $stmt_vw->get_result();

                            if ($result_vw->num_rows > 0) {
                                while ($row_vw = $result_vw->fetch_assoc()) {
                                    $score = $row_vw['points'];
                                    $rank = $row_vw['rank'];
                                    echo "<option value='" . htmlspecialchars($score) . "' " .
                                        ($selectedPoints == $score ? 'selected' : '') . ">" .
                                        htmlspecialchars($rank) . " - " . htmlspecialchars($score) . " pts.</option>";
                                }
                            }
                            $stmt_vw->close();

                            echo "</select>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No contestants found for team " . htmlspecialchars($teamName) . ".</td></tr>";
                    }
                    $stmt->close(); // Close the contestant query statement
                }
                $conn->close();
                ?>

                <tr>
                    <td></td>
                    <td style="text-align: right; padding-right: 5vw;">
                        <button type="button" class="addEdit-btn" onclick="addEdit()">Add/Edit</button>
                        <button type="button" class="saveScore-btn" onclick="saveScores()" style="display:none;">Save Scores</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        // Track selected scores and hide them from other select options
        function updateScores(selectElement) {
            const selectedScores = Array.from(document.querySelectorAll(".teamsScore"))
                .map(select => select.value)
                .filter(value => value !== "0");

            document.querySelectorAll(".teamsScore").forEach(select => {
                Array.from(select.options).forEach(option => {
                    if (selectedScores.includes(option.value) && option.value !== select.value) {
                        option.hidden = true;
                    } else {
                        option.hidden = false;
                    }
                });
            });
        }
    </script>

    <script>
        function addEdit() {
            document.querySelectorAll('.teamsScore').forEach(function(select) {
                select.disabled = false;
            });
            document.querySelector('.addEdit-btn').style.display = 'none';
            document.querySelector('.saveScore-btn').style.display = 'inline-block';
        }

        function saveScores() {
            const scores = [];
            const eventId = <?= json_encode($evId) ?>; // Gets event ID from PHP variable
            const eventName = <?= json_encode($evname) ?>; // Gets event name from PHP variable

            document.querySelectorAll('.teamRow').forEach(row => {
                const contestantId = row.querySelector('.contestantId').value;
                const score = row.querySelector('.teamsScore').value;
                scores.push({
                    contestantId,
                    score
                });
            });

            console.log('Data to be sent:', {
                eventId,
                eventName,
                scores
            }); // Debugging info

            // Send the data as JSON
            fetch(`saveScores.php?name=${eventName}`, { // Corrected here
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        eventId,
                        eventName,
                        scores
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Scores have been saved successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });

                        document.querySelectorAll('.teamsScore').forEach(select => select.disabled = true);
                        document.querySelector('.saveScore-btn').style.display = 'none';
                        document.querySelector('.addEdit-btn').style.display = 'inline-block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to save scores.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }
    </script>
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

                // Store the original values of dropdowns in the row
                row.querySelectorAll('select').forEach(select => {
                    select.dataset.originalValue = select.value;
                    select.disabled = false; // Enable dropdowns for editing
                });

                // Hide the Edit button and show the Save and Cancel buttons
                row.querySelector('.edit-btn').style.display = 'none';
                row.querySelector('.save-btn').style.display = 'inline-block';
                row.querySelector('.cancel-btn').style.display = 'inline-block';

                // Disable all other Edit buttons to allow only one row edit at a time
                document.querySelectorAll('.edit-btn').forEach(btn => btn.disabled = true);
            });
        });

        // Cancel button functionality
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');

                // Restore the original values of dropdowns in the row
                row.querySelectorAll('select').forEach(select => {
                    select.value = select.dataset.originalValue; // Revert to the original value
                    select.disabled = true; // Disable dropdowns again
                });

                // Show the Edit button and hide the Save and Cancel buttons
                row.querySelector('.edit-btn').style.display = 'inline-block';
                row.querySelector('.save-btn').style.display = 'none';
                row.querySelector('.cancel-btn').style.display = 'none';

                // Re-enable all Edit buttons
                document.querySelectorAll('.edit-btn').forEach(btn => btn.disabled = false);
                // Reload the page
                location.reload(); // This will refresh the page
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

                // Log the data being sent
                console.log('Sending data:');
                console.log('eventId:', eventId);
                console.log('teamA:', teamA);
                console.log('teamB:', teamB);

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

                        document.querySelectorAll('.edit-btn').forEach(btn => btn.disabled = false);

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
                xhr.send(`eventId=${eventId}&teamA=${teamA}&teamB=${teamB}`);
            });
        });

        // LOGOUT CONFIRMATION
        document.getElementById('logout').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7FD278',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log me out',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // mag redirect siya to the login page
                    window.location.href = '../committee/Sevents.php?logout';
                }
            });
        });
    </script>

</body>

</html>