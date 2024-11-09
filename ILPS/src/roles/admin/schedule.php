<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php'; // database connection
require_once '../admin/verifyLoginSession.php'; // logged in or not
require_once 'adminPermissions.php'; // Retrieves admin permissions

// call the stored procedure to get all teams
$query_teams = "CALL sp_getAllTeam()";
$result_teams = $conn->query($query_teams);

$teams = [];
if ($result_teams->num_rows > 0) {
    while ($row_team = $result_teams->fetch_assoc()) {
        $teams[$row_team['teamId']] = $row_team['teamName'];
    }
}

// free the result set from the stored procedure
$result_teams->free();
$conn->next_result(); // Make sure to prepare for the next query

// fetch scheduled days
$query_days = "CALL sp_getDay()";
$result_days = $conn->query($query_days);

$scheduled_days = [];

if ($result_days->num_rows > 0) {
    while ($row_day = $result_days->fetch_assoc()) {
        $day_id = $row_day['id'];

        // Free the result of the first stored procedure call
        $conn->next_result();

        // Fetch events for each day
        $query_events = "CALL sp_getScheduledEvent(?)";
        $stmt_events = $conn->prepare($query_events);
        $stmt_events->bind_param("i", $day_id);
        $stmt_events->execute();
        $result_events = $stmt_events->get_result();

        $events = [];
        if ($result_events->num_rows > 0) {
            while ($row_event = $result_events->fetch_assoc()) {
                // Fetch team names using the team IDs from the stored procedure result
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

        // Free the result of the second stored procedure call
        $stmt_events->free_result();
        $conn->next_result();  // Ensure there's no remaining result

        $row_day['events'] = $events;
        $scheduled_days[] = $row_day;
    }
}

// Free the result of the outermost call
$result_days->free();
$conn->next_result();


usort($scheduled_days, function ($a, $b) {
    return strtotime($a['day_date']) - strtotime($b['day_date']);
});
?>

<!DOCTYPE html>
<html>

<head>
    <title>Schedule</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/schedule.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
            <p onclick="window.location.href = 'admin.php';" class="navbar" title="Home">Home</p>
            <div class="acc-hover">
                <div class="acc-btn-container">
                    <p onclick="window.location.href = 'accounts.php';" class="navbar" ; title="Accounts">Accounts</p>
                </div>
                <div class="account-dropdown">
                    <p onclick="window.location.href = 'roles.php';" class="dc-text">Role</p>
                </div>
            </div>
            <p onclick="window.location.href = 'teams.php';" class="navbar" title="Teams">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="navbar" title="Events">Events</p>
            <p onclick="window.location.href = 'schedule.php';" class="navbarbie" title="Schedule">Schedule</p>
        </nav>
        <nav class="nav-link-1">
            <div class="dropdown">
                <button class="dropbtn">
                    <img class="icon-img" src="../../../public/assets/icons/icon-user.jpg">
                    <div>
                        <p class="user-name"><?php echo $admin_name; ?></p>
                        <p class="administrator">ADMINISTRATOR</p>
                    </div>
                </button>
                <div class="dropdown-content">
                    <p onclick="window.location.href = 'view-profile.php';" class="dc-text" title="Profile">View Profile</p>
                    <p onclick="window.location.href = 'reports.php';" class="dc-text" title="Reports">Reports</p>
                    <p onclick="window.location.href = 'accesslog.php';" class="dc-text" title="Logs">Logs</p>
                    <p onclick="showConfirmationMsg()" class="dc-text" title="Backup">Backup and Reset</p>
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <?php
    // Display Schedule - permitted to view
    if (in_array('schedule_read', $admin_rights)) {
        // Display Add Day - permitted to add
        if (in_array('schedule_add', $admin_rights)) { ?>
            <!-- Add Day Popup -->
            <div class="new-sched" id="openPopup">
                <div class="plus-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div id="addDayBtn">
                    <p id="create">Add Day</p>
                    <p id="add">Add day for intramurals event.</p>
                </div>
            </div>
        <?php } else {
            echo '
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong>FYI: </strong> \'Add Day\' feature is hidden as you don\'t have the permission.
                </div>
            ';
        } ?>
        <div class="schedule">
            <div class="schedule-title">
                <p id="sched">Schedule</p>
            </div>

            <div class="schedule-history">
                <?php if (count($scheduled_days) > 0): ?>
                    <?php $dayCounter = 1; ?>

                    <?php foreach ($scheduled_days as $day): ?>
                        <div class="new-sched day-schedule" id="daySchedule-<?php echo $day['id']; ?>" data-day-id="<?php echo $day['id']; ?>">
                            <div class="day-info">
                                <div class="day-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="day-info-info">
                                    <p class="day-title">Day <?php echo $dayCounter++; ?></p>
                                    <p class="day-date"><?php echo date('F j, Y', strtotime($day['day_date'])); ?></p>
                                </div>
                            </div>

                            <div class="header-right">
                                <?php // Display edit button - permitted to update
                                if (in_array('schedule_update', $admin_rights)) { ?>
                                    <button id="edit-icon" class="header-btn editHeaderBtn" data-day-id="<?php echo $day['id']; ?>" data-day-date="<?php echo $day['day_date']; ?>"><i class="fa-solid fa-pen-to-square"></i></i></button>
                                <?php }
                                // Display delete button - permitted to delete
                                if (in_array('schedule_delete', $admin_rights)) { ?>
                                    <button id="trash-icon" class="header-btn deleteHeaderBtn" data-day-id="<?php echo $day['id']; ?>" data-day-date="<?php echo $day['day_date']; ?>"><i class="fa-solid fa-trash-can"></i></button>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Events for the day (Initially hidden) -->
                        <div class="day-events" id="dayEvents-<?php echo $day['id']; ?>" style="display: none;">
                            <?php // Display scheduled event table - permitted to view
                            if (in_array('scheduledEvent_read', $admin_rights)) { ?>
                                <table id="scheduleTable-<?php echo $day['id']; ?>">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Type</th>
                                            <th>Activity</th>
                                            <th>Game No.</th>
                                            <th>Team A</th>
                                            <th>Team B</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($day['events']) == 0): ?>
                                            <tr>
                                                <td colspan="10" style="text-align: center;">No events scheduled for this day.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($day['events'] as $event): ?>
                                                <tr data-event-id="<?php echo $event['id']; ?>">
                                                    <td><?php echo date('h:i A', strtotime($event['time'])); ?></td>
                                                    <td><?php echo $event['type']; ?></td>
                                                    <td><?php echo $event['activity']; ?></td>
                                                    <td><?php echo $event['gameNo']; ?></td>
                                                    <td><?php echo $event['teamA_name']; ?></td>
                                                    <td><?php echo $event['teamB_name']; ?></td>
                                                    <td><?php echo $event['location']; ?></td>
                                                    <td><?php echo $event['status']; ?></td>
                                                    <td>
                                                        <?php // Display edit button - permitted to update
                                                        if (in_array('scheduledEvent_update', $admin_rights)) { ?>
                                                            <button class="edit-btn" data-event-id="<?php echo $event['id']; ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <?php }
                                                        // Display delete button - permitted to delete
                                                        if (in_array('scheduledEvent_delete', $admin_rights)) { ?>
                                                            <button class="delete-btn"><i class="fa-solid fa-trash-can"></i></button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <?php // Display add event button - permitted to add
                                if (in_array('scheduledEvent_add', $admin_rights)) { ?>
                                    <button class="addEventBtn" id="addEventBtn" data-day-id="<?php echo $day['id']; ?>">Add Event</button>
                                <?php } ?>
                            <?php } else {
                                echo '
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Oops!</strong> You lack the permission to view the Scheduled Event/s.
                            </div>
                        ';
                            } ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center;">No schedule available. Please add new days.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
            document.querySelectorAll('.day-schedule').forEach(daySchedule => {
                daySchedule.addEventListener('click', function() {
                    const dayId = this.getAttribute('data-day-id');
                    const eventsDiv = document.getElementById(`dayEvents-${dayId}`);

                    if (eventsDiv.style.display === 'block') {
                        eventsDiv.style.display = 'none';
                    } else {
                        document.querySelectorAll('[id^="dayEvents-"]').forEach(eventDiv => {
                            eventDiv.style.display = 'none';
                        });
                        eventsDiv.style.display = 'block';
                    }
                });
            });

            // Prevent toggle when clicking Edit or Delete buttons
            document.querySelectorAll('.header-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });

            /* TIME SORTING */
            function sortTableByTime(tableId) {
                const table = document.getElementById(tableId);
                const rows = Array.from(table.rows).slice(1);

                rows.sort((a, b) => {
                    const timeA = a.cells[0].getAttribute('data-time');
                    const timeB = b.cells[0].getAttribute('data-time');

                    return timeA.localeCompare(timeB);
                });

                rows.forEach(row => table.appendChild(row));
            }

            /* LOGOUT CONFIRMATION */
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
                        window.location.href = 'schedule.php?logout';
                    }
                });
            });

            document.getElementById('openPopup').addEventListener('click', function() {
                Swal.fire({
                    title: 'Add New Day',
                    html: ` 
                    <input id="new-day-date" class="swal2-input1" placeholder="Date" type="date">
                `,
                    confirmButtonText: 'Add',
                    showCancelButton: true,
                    preConfirm: () => {
                        const dayDate = document.getElementById('new-day-date').value;
                        const today = new Date().toISOString().split('T')[0];

                        if (!dayDate) {
                            Swal.showValidationMessage('Please enter a Date');
                            return false;
                        }

                        if (dayDate < today) {
                            Swal.showValidationMessage('Date cannot be in the past');
                            return false;
                        }

                        return {
                            dayDate: dayDate
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {
                            dayDate
                        } = result.value;

                        const xhrCheck = new XMLHttpRequest();
                        xhrCheck.open("POST", "get_existing_dates.php", true);
                        xhrCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhrCheck.onload = function() {
                            if (xhrCheck.status === 200) {
                                const response = JSON.parse(xhrCheck.responseText);

                                if (response.exists) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'This date already exists. Please choose another date.',
                                        icon: 'error',
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    const xhrAdd = new XMLHttpRequest();
                                    xhrAdd.open("POST", "add_day.php", true);
                                    xhrAdd.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                    xhrAdd.onload = function() {
                                        if (xhrAdd.status === 200) {
                                            const addResponse = JSON.parse(xhrAdd.responseText);
                                            if (addResponse.success) {
                                                Swal.fire({
                                                    title: 'Success!',
                                                    text: 'New day added successfully.',
                                                    icon: 'success',
                                                    confirmButtonColor: '#7FD278',
                                                    confirmButtonText: 'OK'
                                                }).then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: 'Error!',
                                                    text: addResponse.message,
                                                    icon: 'error',
                                                    confirmButtonColor: '#d33',
                                                    confirmButtonText: 'OK'
                                                });
                                            }
                                        }
                                    };
                                    xhrAdd.send(`day_date=${dayDate}`);
                                }
                            }
                        };
                        xhrCheck.send(`day_date=${dayDate}`);
                    }
                });
            });


            // Delete date
            document.querySelectorAll('.deleteHeaderBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const dayId = this.getAttribute('data-day-id');
                    const dayDate = this.getAttribute('data-day-date');

                    // Get the current date and compare it with the dayId
                    const today = new Date();
                    const selectedDate = new Date(dayId);

                    // Check if the selected date is in the past
                    if (selectedDate < today.setHours(0, 0, 0, 0)) {
                        Swal.fire({
                            title: 'Action Not Allowed',
                            text: 'This date cannot be deleted as it has already passed.',
                            icon: 'error'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7FD278',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const xhr = new XMLHttpRequest();
                            xhr.open("POST", "delete_day.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Schedule has been deleted.',
                                            icon: 'success',
                                            confirmButtonColor: '#7FD278',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            location.reload(); // Reload the page to reflect changes
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message,
                                            icon: 'error',
                                            confirmButtonColor: '#d33',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to delete the schedule.',
                                        icon: 'error',
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            };

                            xhr.send(`day_id=${dayId}&day_date=${dayDate}`);
                        }
                    });
                });
            });


            // Add event to specific date
            document.querySelectorAll('.addEventBtn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const dayId = button.getAttribute('data-day-id');

                    // Get the current date and compare it with the dayId
                    const today = new Date();
                    const selectedDate = new Date(dayId); // Assumes dayId is in a format that can be parsed by Date (e.g., 'YYYY-MM-DD')

                    // Check if the selected date is in the past
                    if (selectedDate < today.setHours(0, 0, 0, 0)) { // Sets today's time to midnight for an accurate comparison
                        Swal.fire({
                            title: 'Action Not Allowed',
                            text: 'You cannot add an event to a past date.',
                            icon: 'error'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Add New Event',
                        html: `
                        <input id="event-time" class="swal2-input1" placeholder="Time" type="time">
                        <select id="event-category" class="swal2-input3">
                            <option value="" disabled selected>Type</option>
                            <option value="socio-cultural">Socio-cultural</option>
                            <option value="sports">Sports</option>
                            <option value="others">Others</option>
                        </select>
                        <select id="event-sports" class="swal2-input3" style="display: none;" required style="display: none;">
                            <option value="" disabled selected>Event</option>
                            <?php
                            $sql = "CALL sp_getEventFrom(?);";
                            $ev_type = "Sports";

                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $ev_type);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $db_eventId = $row['eventID'];
                                    $db_evname = $row['eventName'];
                            ?>
                                    <option value="<?php echo $db_eventId; ?>" data-event-name="<?php echo htmlspecialchars($db_evname); ?>">
                                        <?php echo htmlspecialchars($db_evname); ?>
                                    </option>
                                    <?php
                                }
                            } else {
                                echo '<option selected disabled value=0>No Event/s exist.</option>';
                            }
                            $result->free();
                            $stmt->close();
                                    ?>
                        </select>
                        <select id="event-socio" class="swal2-input3" required style="display: none;">
                            <option value="" disabled selected>Event</option>
                                <?php
                                $sql = "CALL sp_getEventFrom(?);";
                                $ev_type = "Socio-Cultural";

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $ev_type);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $db_evname = $row['eventName'];
                                ?>
                                        <option value="<?php echo htmlspecialchars($db_evname); ?>" data-type="<?php echo htmlspecialchars($row['eventType']); ?>">
                                            <?php echo htmlspecialchars($db_evname); ?>
                                        </option>
                                <?php
                                    }
                                } else {
                                    echo '<option selected disabled value=0>No Event/s exist.</option>';
                                }
                                $result->free();
                                $stmt->close();
                                ?>
                        </select>
                        <input id="event-activity-others" class="swal2-input2" placeholder="Activity" style="display: none;">
                        <input id="event-game-number" class="swal2-input2" placeholder="Game Number" style="display: none;" type="number" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        <select id="team-a" class="swal2-input3" style="display: none;" data-event-id="">
                            <option value="" disabled selected>Team A</option>
                        </select>
                        <select id="team-b" class="swal2-input3" style="display: none;">
                            <option value="" disabled selected>Team B</option>
                        </select>
                        <input id="event-location" class="swal2-input2" placeholder="Location">
                    `,
                        confirmButtonText: 'Add',
                        showCancelButton: true,
                        preConfirm: () => {
                            const time24 = document.getElementById('event-time').value;
                            const type = document.getElementById('event-category').value;
                            const eventSports = document.getElementById('event-sports').selectedOptions[0].getAttribute('data-event-name');
                            const eventSocio = document.getElementById('event-socio').value;
                            const activityOthers = document.getElementById('event-activity-others').value;
                            const gameNumber = document.getElementById('event-game-number').value;
                            const teamA = document.getElementById('team-a').value;
                            const teamB = document.getElementById('team-b').value;
                            const location = document.getElementById('event-location').value
                                .toLowerCase()
                                .replace(/\b\w/g, char => char.toUpperCase());
                            const status = "Pending";

                            if (!time24 || !type || !location || (!eventSports && !eventSocio && !activityOthers)) {
                                Swal.showValidationMessage('Please fill in all required fields');
                                return false;
                            }

                            const [hour, minute] = time24.split(':');
                            const hours = parseInt(hour);
                            const period = hours >= 12 ? 'PM' : 'AM';
                            const hour12 = (hours % 12) || 12;
                            const time12 = `${hour12}:${minute} ${period}`;

                            let activity;
                            if (type === 'sports') {
                                activity = eventSports;
                            } else if (type === 'socio-cultural') {
                                activity = eventSocio;
                            } else if (type === 'others') {
                                activity = activityOthers;
                                activity = activity.replace(/\b\w/g, char => char.toUpperCase()); // capitalize other events
                            }

                            return {
                                time: time12,
                                type,
                                activity,
                                gameNumber,
                                teamA,
                                teamB,
                                location,
                                status
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                time,
                                type,
                                activity,
                                gameNumber,
                                teamA,
                                teamB,
                                location,
                                status
                            } = result.value;

                            const xhr = new XMLHttpRequest();
                            xhr.open("POST", "add_event.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.onload = function() {
                                console.log(xhr.responseText); // log the response for debugging
                                if (xhr.status === 200) {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.success) {
                                        const table = document.querySelector(`#scheduleTable-${dayId} tbody`);

                                        const noEventsRow = table.querySelector('tr td[colspan="10"]');
                                        if (noEventsRow) {
                                            noEventsRow.parentNode.removeChild(noEventsRow);
                                        }

                                        const newRow = table.insertRow();
                                        newRow.innerHTML = `
                                        <td>${time}</td>
                                        <td>${type.charAt(0).toUpperCase() + type.slice(1)}</td>
                                        <td>${activity}</td>
                                        <td>${gameNumber}</td>
                                        <td>${response.teamA_name}</td>
                                        <td>${response.teamB_name}</td>
                                        <td>${location}</td>
                                        <td>${status}</td>
                                        <td>
                                            <button class="edit-btn" data-event-id="${dayId}">Edit</button>
                                            <button class="delete-btn">Delete</button>
                                        </td>
                                    `;
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.message,
                                            icon: 'success'
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message,
                                            icon: 'error'
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to communicate with server.',
                                        icon: 'error'
                                    });
                                }
                            };
                            xhr.send(`day_id=${dayId}&time=${time}&type=${type}&activity=${activity}&location=${location}&game_number=${gameNumber}&teamA=${teamA}&teamB=${teamB}&status=${status}`);
                        }
                    });


                    document.getElementById('event-sports').addEventListener('change', function() {
                        const eventId = this.value;
                        if (eventId) {
                            const xhr = new XMLHttpRequest();
                            xhr.open("POST", "get_eventContestants.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    const response = JSON.parse(xhr.responseText);
                                    const teamASelect = document.getElementById('team-a');
                                    const teamBSelect = document.getElementById('team-b');
                                    const gameNo = document.getElementById('event-game-number');

                                    teamASelect.innerHTML = '<option value="" disabled selected>Team A</option>';
                                    teamBSelect.innerHTML = '<option value="" disabled selected>Team B</option>';

                                    if (response.success && response.teams.length > 0) {
                                        response.teams.forEach(team => {
                                            const optionA = document.createElement('option');
                                            optionA.value = team.teamId;
                                            optionA.textContent = `${team.teamName}`;
                                            teamASelect.appendChild(optionA);

                                            const optionB = document.createElement('option');
                                            optionB.value = team.teamId;
                                            optionB.textContent = `${team.teamName}`;
                                            teamBSelect.appendChild(optionB);
                                        });

                                        // Show the team dropdowns
                                        teamASelect.style.display = 'block';
                                        teamBSelect.style.display = 'block';
                                        gameNo.style.display = 'block';


                                        // Clear any validation messages
                                        if (Swal.isVisible()) {
                                            Swal.resetValidationMessage();
                                        }
                                    } else {
                                        // Hide the dropdowns if no teams are found
                                        teamASelect.style.display = 'none';
                                        teamBSelect.style.display = 'none';
                                        gameNo.style.display = 'none';

                                        // Show validation message only when no teams are found
                                        Swal.showValidationMessage("No teams found for the selected event.");
                                    }
                                } else {
                                    alert('Error communicating with the server.');
                                }
                            };

                            xhr.send(`event_id=${eventId}`);
                        }
                    });


                    // JavaScript to toggle visibility of the activity inputs based on category selection
                    document.getElementById('event-category').addEventListener('change', function() {
                        const activitySports = document.getElementById('event-sports');
                        const activitySocio = document.getElementById('event-socio');
                        const activityOthers = document.getElementById('event-activity-others');
                        const gameNo = document.getElementById('event-game-number');
                        const teamA = document.getElementById('team-a');
                        const teamB = document.getElementById('team-b');

                        if (this.value === 'sports') {
                            activitySports.style.display = 'block';
                            gameNo.style.display = 'none';
                            teamA.style.display = 'none';
                            activityOthers.style.display = 'none';
                            activitySocio.style.display = 'none';
                        } else if (this.value === 'others') {
                            activitySports.style.display = 'none';
                            activitySocio.style.display = 'none';
                            gameNo.style.display = 'none';
                            teamA.style.display = 'none';
                            teamB.style.display = 'none';
                            activityOthers.style.display = 'block';
                        } else if (this.value === 'socio-cultural') {
                            teamA.style.display = 'none';
                            activitySports.style.display = 'none';
                            activitySocio.style.display = 'block';
                            teamB.style.display = 'none';
                            activityOthers.style.display = 'none';
                            gameNo.style.display = 'none';
                        } else {
                            teamA.style.display = 'none';
                            gameNo.style.display = 'none';
                            teamB.style.display = 'none';
                            activitySocio.style.display = 'none';
                            activitySports.style.display = 'none';
                            activityOthers.style.display = 'none';
                        }
                    });

                    document.getElementById('team-a').addEventListener('change', function() {
                        const selectedTeamA = this.value;
                        const teamB = document.getElementById('team-b');

                        teamB.style.display = 'block';

                        // clear previous options in Team B
                        teamB.innerHTML = '<option value="" disabled selected>Team B</option>';

                        // get all options from Team A
                        const teamAOptions = document.querySelectorAll('#team-a option');

                        // add options to Team B excluding the selected Team A option
                        teamAOptions.forEach(option => {
                            if (option.value && option.value !== selectedTeamA) {
                                const newOption = document.createElement('option');
                                newOption.value = option.value;
                                newOption.textContent = option.textContent;
                                teamB.appendChild(newOption);
                            }
                        });
                    });
                });
            });

            document.querySelectorAll('.edit-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const eventId = row.getAttribute('data-event-id');

                    console.log("Event ID:", eventId);

                    // fetch existing event
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "get_existing_sched_events.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                const fetchedEvent = response.event;
                                const dayId = fetchedEvent.day_id;

                                console.log("Fetched Day ID:", dayId);

                                // check if  event date is past
                                const xhrDateCheck = new XMLHttpRequest();
                                xhrDateCheck.open("POST", "check_event_date.php", true);
                                xhrDateCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                xhrDateCheck.onload = function() {
                                    if (xhrDateCheck.status === 200) {
                                        const dateResponse = JSON.parse(xhrDateCheck.responseText);

                                        console.log("Event Date:", dateResponse.debug?.eventDate);
                                        console.log("Current Date:", dateResponse.debug?.currentDate);

                                        // f the event is (past date, update status to "Ended"
                                        if (!dateResponse.success || !dateResponse.editable) {
                                            console.log("The event date is past; updating status to 'Ended'.");

                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Action Not Allowed',
                                                text: 'This event is no longer editable as it has already passed.',
                                                confirmButtonText: 'Okay'
                                            }).then(() => {
                                                window.location.reload();
                                            });

                                            const xhrUpdateStatus = new XMLHttpRequest();
                                            xhrUpdateStatus.open("POST", "update_event_status.php", true);
                                            xhrUpdateStatus.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                            xhrUpdateStatus.onload = function() {
                                                if (xhrUpdateStatus.status === 200) {
                                                    const updateResponse = JSON.parse(xhrUpdateStatus.responseText);
                                                    console.log(updateResponse.message);
                                                }
                                            };
                                            xhrUpdateStatus.send("day_id=" + encodeURIComponent(dayId));
                                            return;
                                        } else {
                                            console.log("The event date has not yet passed; proceeding to edit.");
                                        }

                                        console.log('Event type fetched from database:', fetchedEvent.type);

                                        // Set category options based on fetched event type
                                        let categoryOptions = '';
                                        if (fetchedEvent.type === 'Socio-Cultural') {
                                            categoryOptions = `
                                                <option value="${fetchedEvent.type}">${fetchedEvent.type}</option>
                                                <option value="sports">Sports</option>
                                                <option value="others">Others</option>
                                            `;
                                        } else if (fetchedEvent.type === 'Sports') {
                                            categoryOptions = `
                                                <option value="${fetchedEvent.type}">${fetchedEvent.type}</option>
                                                <option value="Socio-Cultural">Socio-Cultural</option>
                                                <option value="others">Others</option>
                                            `;
                                        } else {
                                            categoryOptions = `
                                                <option value="${fetchedEvent.type}">${fetchedEvent.type}</option>
                                                <option value="Socio-Cultural">Socio-Cultural</option>
                                                <option value="sports">Sports</option>
                                            `;
                                        }

                                        // If event type is "Others," no need to fetch teams
                                        if (fetchedEvent.type === 'Others') {
                                            showEditEventModal(fetchedEvent, categoryOptions, []); // No teams needed
                                            return;
                                        }

                                        const xhrTeams = new XMLHttpRequest();
                                        xhrTeams.open("POST", "get_teamsForSched.php", true);
                                        xhrTeams.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                        xhrTeams.onload = function() {
                                            if (xhrTeams.status === 200) {
                                                const teamResponse = JSON.parse(xhrTeams.responseText);
                                                const teams = teamResponse.success ? teamResponse.teams : [];
                                                showEditEventModal(fetchedEvent, categoryOptions, teams);
                                            }
                                        };
                                        xhrTeams.send("event_id=" + encodeURIComponent(eventId));
                                    }
                                };
                                xhrDateCheck.send("day_id=" + encodeURIComponent(dayId));
                            }
                        }
                    };
                    xhr.send("event_id=" + encodeURIComponent(eventId));
                });
            });



            // modal connected to edit
            function showEditEventModal(fetchedEvent, categoryOptions, teams, ) {
                let teamOptionsA = '<option value="" disabled selected>Team A</option>';
                let teamOptionsB = '<option value="" disabled selected>Team B</option>';

                teams.forEach(team => {
                    teamOptionsA += `<option value="${team.teamId}" ${team.teamId === fetchedEvent.teamA ? 'selected' : ''}>${team.teamName}</option>`;
                });

                // Filter Team B options based on Team A
                teams.forEach(team => {
                    if (team.teamId !== fetchedEvent.teamA) {
                        teamOptionsB += `<option value="${team.teamId}" ${team.teamId === fetchedEvent.teamB ? 'selected' : ''}>${team.teamName}</option>`;
                    }
                });

                Swal.fire({
                    title: 'Edit Event',
                    html: `
                                <input type="hidden" id="event-id" value="${fetchedEvent.id}">
                                <input type="hidden" id="day-id" value="${fetchedEvent.day_id}">

                                <input id="edit-time" class="swal2-input1" type="time" value="${fetchedEvent.time}">
                                <select id="edit-event-category" class="swal2-input3">
                                    ${categoryOptions}
                                </select>
                                <select id="edit-event-sports" class="swal2-input3" style="display: none;" required>
                                    <option value="${fetchedEvent.activity}" selected disabled> ${fetchedEvent.activity}</option>
                                        <?php
                                        $sql = "CALL sp_getEventFrom(?);";
                                        $ev_type = "Sports";

                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("s", $ev_type);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $db_eventId = $row['eventID'];
                                                $db_evname = $row['eventName'];
                                        ?>
                                                        <option value="<?php echo $db_eventId; ?>" data-event-name="<?php echo htmlspecialchars($db_evname); ?>">
                                                            <?php echo htmlspecialchars($db_evname); ?>
                                                        </option>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo '<option selected disabled value=0>No Event/s exist.</option>';
                                                    }
                                                    $result->free();
                                                    $stmt->close();
                                                            ?>
                                                </select>
                                                <select id="edit-event-socio" class="swal2-input3" style="display: none;" required>
                                                    <option value="${fetchedEvent.activity}" selected disabled> ${fetchedEvent.activity}</option>
                                                    <?php
                                                    $sql = "CALL sp_getEventFrom(?);";
                                                    $ev_type = "Socio-Cultural";

                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bind_param("s", $ev_type);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            $db_evname = $row['eventName'];
                                                    ?>
                                                            <option value="<?php echo htmlspecialchars($db_evname); ?>" data-type="<?php echo htmlspecialchars($row['eventType']); ?>">
                                                                <?php echo htmlspecialchars($db_evname); ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo '<option selected disabled value=0>No Event/s exist.</option>';
                                                    }
                                                    $result->free();
                                                    $stmt->close();
                                                            ?>
                                                </select>
                                                <input id="edit-event-activity-others" class="swal2-input2" placeholder="Activity" value="${fetchedEvent.activity}" style="display: none;">
                                                <input id="edit-game-no" class="swal2-input2" placeholder="Game Number" value="${fetchedEvent.gameNo !== null ? fetchedEvent.gameNo : ''}" style="display: none;">
                                                <select id="edit-team-a" class="swal2-input3" required>
                                                    ${teamOptionsA}
                                                </select>
                                                <select id="edit-team-b" class="swal2-input3" required>
                                                    ${teamOptionsB}
                                                </select>
                                                <input id="edit-location" class="swal2-input2" placeholder="Location" value="${fetchedEvent.location}">
                                                <select id="edit-status" class="swal2-input3">
                                                    <option value="Pending" ${fetchedEvent.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                                    <option value="Ongoing" ${fetchedEvent.status === 'Ongoing' ? 'selected' : ''}>On-going</option>
                                                    <option value="Ended" ${fetchedEvent.status === 'Ended' ? 'selected' : ''}>Ended</option>
                                                    <option value="Cancelled" ${fetchedEvent.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                                    <option value="Moved" ${fetchedEvent.status === 'Moved' ? 'selected' : ''}>Moved</option>
                                                </select>
                                            `,
                    didOpen: () => {
                        const categorySelect = document.getElementById('edit-event-category');
                        const eventSportsSelect = document.getElementById('edit-event-sports');
                        const eventSocioSelect = document.getElementById('edit-event-socio');
                        const activityOthersInput = document.getElementById('edit-event-activity-others');
                        const gameNoInput = document.getElementById('edit-game-no');

                        function toggleGameFields(category) {
                            const teamAField = document.getElementById('edit-team-a');
                            const teamBField = document.getElementById('edit-team-b');

                            // filter team B based on the selected team A
                            function filterTeamBOptions(selectedTeamA) {
                                // re-populate team B options based on team A
                                teamBField.innerHTML = '<option value="" disabled>Team B</option>';
                                teams.forEach(team => {
                                    if (team.teamId !== selectedTeamA) {
                                        const newOption = document.createElement('option');
                                        newOption.value = team.teamId;
                                        newOption.textContent = team.teamName;
                                        teamBField.appendChild(newOption);
                                    }
                                });

                                if (fetchedEvent.teamB) {
                                    teamBField.value = fetchedEvent.teamB;
                                }
                            }

                            filterTeamBOptions(fetchedEvent.teamA);

                            teamAField.addEventListener('change', () => {
                                filterTeamBOptions(teamAField.value);
                            });

                            gameNoInput.style.display = 'none';
                            teamAField.style.display = 'none';
                            teamBField.style.display = 'none';
                            activityOthersInput.style.display = 'none';
                            eventSportsSelect.style.display = 'none';
                            eventSocioSelect.style.display = 'none';

                            if (category === 'sports') {
                                activityOthersInput.style.display = 'none';
                                gameNoInput.style.display = 'block';
                                teamAField.style.display = 'block';
                                teamBField.style.display = 'block';
                                eventSportsSelect.style.display = 'block';

                                //if changed type
                                activityOthersInput.value = '';
                                const socioDropdown = document.getElementById('edit-event-socio');
                                socioDropdown.selectedIndex = -1;
                            } else if (category === 'socio-cultural') {
                                activityOthersInput.style.display = 'none';
                                eventSocioSelect.style.display = 'block';

                                //if changed type
                                activityOthersInput.value = '';
                                const sportsDropdown = document.getElementById('edit-event-sports');
                                sportsDropdown.selectedIndex = -1;
                            } else if (category === 'others') {
                                activityOthersInput.style.display = 'block';

                                //if changed type
                                const socioDropdown = document.getElementById('edit-event-socio');
                                socioDropdown.selectedIndex = -1;
                                const sportsDropdown = document.getElementById('edit-event-sports');
                                sportsDropdown.selectedIndex = -1;
                            }
                        }

                        toggleGameFields(fetchedEvent.type.toLowerCase());

                        categorySelect.addEventListener('change', function() {
                            toggleGameFields(this.value.toLowerCase());
                        });

                        document.getElementById('edit-team-a').addEventListener('change', function() {
                            const selectedTeamA = this.value;
                            const teamB = document.getElementById('edit-team-b');

                            teamB.style.display = 'block';

                            teamB.innerHTML = '<option value="" disabled selected>Team B</option>';

                            const teamAOptions = document.querySelectorAll('#edit-team-a option');

                            teamAOptions.forEach(option => {
                                if (option.value && option.value !== selectedTeamA) {
                                    const newOption = document.createElement('option');
                                    newOption.value = option.value;
                                    newOption.textContent = option.textContent;
                                    teamB.appendChild(newOption);
                                }
                            });
                        });
                    },
                    confirmButtonText: 'Save',
                    showCancelButton: true,
                    preConfirm: () => {
                        const eventId = document.getElementById('event-id').value;
                        const dayId = document.getElementById('day-id').value;
                        const time24 = document.getElementById('edit-time').value;
                        const type = document.getElementById('edit-event-category').value;
                        let activity;

                        const sportsElement = document.getElementById('edit-event-sports');
                        const socioElement = document.getElementById('edit-event-socio');
                        const othersElement = document.getElementById('edit-event-activity-others');

                        if (sportsElement.style.display !== 'none') {
                            activity = sportsElement.options[sportsElement.selectedIndex].text;
                        } else if (socioElement.style.display !== 'none') {
                            activity = socioElement.options[socioElement.selectedIndex].text;
                        } else if (othersElement.style.display !== 'none') {
                            activity = othersElement.value;
                        } else {
                            activity = '';
                        }
                        const gameNo = document.getElementById('edit-game-no').value || null;
                        const teamA = document.getElementById('edit-team-a').value || null;
                        const teamB = document.getElementById('edit-team-b').value || null;
                        const location = document.getElementById('edit-location').value
                            .toLowerCase()
                            .replace(/\b\w/g, char => char.toUpperCase());
                        const status = document.getElementById('edit-status').value;

                        console.log('Event:', eventId);
                        console.log('Day:', dayId);
                        console.log('Time:', time24);
                        console.log('Type:', type);
                        console.log('Activity:', activity);
                        console.log('Game Number:', gameNo);
                        console.log('Team A:', teamA);
                        console.log('Team B:', teamB);
                        console.log('Location:', location);
                        console.log('Status:', status);

                        return {
                            eventId,
                            dayId,
                            time: time24,
                            type,
                            activity,
                            gameNo,
                            teamA,
                            teamB,
                            location,
                            status
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {
                            eventId,
                            dayId,
                            time,
                            type,
                            activity,
                            gameNo,
                            teamA,
                            teamB,
                            location,
                            status
                        } = result.value;

                        console.log(result.value);

                        const xhrUpdate = new XMLHttpRequest();
                        xhrUpdate.open("POST", "edit_event.php", true);
                        xhrUpdate.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhrUpdate.onload = function() {
                            console.log('Response:', xhrUpdate.responseText);
                            if (xhrUpdate.status === 200) {
                                const updateResponse = JSON.parse(xhrUpdate.responseText);
                                console.log('Update Response:', updateResponse);
                                if (updateResponse.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Event updated successfully.',
                                        icon: 'success'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    console.error('Error:', updateResponse.message);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: updateResponse.message,
                                        icon: 'error'
                                    });
                                }
                            }
                        };
                        const params = `eventId=${encodeURIComponent(result.value.eventId)}&dayId=${encodeURIComponent(result.value.dayId)}&time=${encodeURIComponent(result.value.time)}&type=${encodeURIComponent(result.value.type)}&activity=${encodeURIComponent(result.value.activity)}&gameNo=${encodeURIComponent(result.value.gameNo || '')}&teamA=${encodeURIComponent(result.value.teamA || '')}&teamB=${encodeURIComponent(result.value.teamB || '')}&location=${encodeURIComponent(result.value.location)}&status=${encodeURIComponent(result.value.status)}`;
                        console.log(params);
                        xhrUpdate.send(params);
                    }
                });
            }




            //edit date
            document.querySelectorAll('.editHeaderBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const dayId = this.getAttribute('data-day-id');
                    const dateDay = this.getAttribute('data-day-date');

                    Swal.fire({
                        title: 'Edit Date',
                        html: ` 
                        <input id="new-day-date" class="swal2-input1" type="date" value="${dateDay}">
                    `,
                        confirmButtonText: 'Save',
                        showCancelButton: true,
                        preConfirm: () => {
                            const dayDate = document.getElementById('new-day-date').value;
                            const today = new Date().toISOString().split('T')[0];

                            if (!dayDate) {
                                Swal.showValidationMessage('Please enter a Date');
                                return false;
                            }

                            if (dayDate < today) {
                                Swal.showValidationMessage('Date cannot be in the past');
                                return false;
                            }

                            return {
                                dayId: dayId,
                                dayDate: dayDate
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                dayId,
                                dayDate
                            } = result.value;

                            const xhrCheck = new XMLHttpRequest();
                            xhrCheck.open("POST", "get_existing_dates.php", true);
                            xhrCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhrCheck.onload = function() {
                                if (xhrCheck.status === 200) {
                                    const response = JSON.parse(xhrCheck.responseText);

                                    if (response.exists) {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'This date already exists. Please choose another date.',
                                            icon: 'error',
                                            confirmButtonColor: '#d33',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        const xhrUpdate = new XMLHttpRequest();
                                        xhrUpdate.open("POST", "edit_day.php", true);
                                        xhrUpdate.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                        xhrUpdate.onload = function() {
                                            if (xhrUpdate.status === 200) {
                                                const updateResponse = JSON.parse(xhrUpdate.responseText);
                                                if (updateResponse.success) {
                                                    Swal.fire({
                                                        title: 'Success!',
                                                        text: 'Day updated successfully.',
                                                        icon: 'success',
                                                        confirmButtonColor: '#7FD278',
                                                        confirmButtonText: 'OK'
                                                    }).then(() => {
                                                        location.reload();
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        title: 'Error!',
                                                        text: updateResponse.message,
                                                        icon: 'error',
                                                        confirmButtonColor: '#d33',
                                                        confirmButtonText: 'OK'
                                                    });
                                                }
                                            }
                                        };
                                        xhrUpdate.send(`day_id=${dayId}&day_date=${dayDate}`);
                                    }
                                }
                            };
                            xhrCheck.send(`day_date=${dayDate}&&day_id=${dayId}`);
                        }
                    });
                });
            });


            function reattachEventListeners() {

                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const eventId = row.getAttribute('data-event-id');

                        // Get the current date and compare it with the dayId
                        const today = new Date();
                        const selectedDate = new Date(eventId); // Assumes dayId is in a format that can be parsed by Date (e.g., 'YYYY-MM-DD')

                        // Check if the selected date is in the past
                        if (selectedDate < today.setHours(0, 0, 0, 0)) { // Sets today's time to midnight for an accurate comparison
                            Swal.fire({
                                title: 'Action Not Allowed',
                                text: 'This event cannot be deleted as it has already passed',
                                icon: 'error'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#7FD278',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', 'delete_event.php', true);
                                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                xhr.onload = function() {
                                    if (xhr.status === 200) {
                                        const response = JSON.parse(xhr.responseText);
                                        if (response.success) {
                                            row.remove();
                                            sortTableByTime(`scheduleTable-${dayId}`);

                                            Swal.fire({
                                                title: 'Deleted!',
                                                text: 'Your event has been deleted.',
                                                icon: 'success',
                                                confirmButtonColor: '#7FD278',
                                                confirmButtonText: 'OK'
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: response.message,
                                                icon: 'error',
                                                confirmButtonColor: '#d33',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    }
                                };

                                xhr.send(`event_id=${eventId}`);
                            }
                        });
                    });
                });

            }

            reattachEventListeners();

            /* BACKUP AND DROP - STARTING A NEW FOR THIS YEARS INTRAMURALS */
            function showConfirmationMsg() {
                // Backup and Drop database confirmation
                Swal.fire({
                    title: 'Database Reset Confirmation',
                    html: "<p style='text-align: left;'><b>Warning</b>: You are about to perform a database backup and reset. This will:</br>1. <b>Download a backup</b> of your current data</br>2. <b>Permanently delete</b> all records from the database</br></br>This action <b><u>cannot be undone</u></b>.</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7FD278',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Backup and Reset',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('../../backup.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'action=backup'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Download the data
                                    downloadBackup(data.file);
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message,
                                        icon: 'error',
                                        confirmButtonColor: '#7FD278',
                                        confirmButtonText: 'OK'
                                    })
                                }
                            })
                            .catch(error => {
                                console.log('Exception Error: ' + error.message);
                            })
                    }
                });
            }

            function downloadBackup(file) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../../backup.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'download';

                const fileInput = document.createElement('input');
                fileInput.type = 'hidden';
                fileInput.name = 'file';
                fileInput.value = file;

                form.appendChild(actionInput);
                form.appendChild(fileInput);
                document.body.appendChild(form);
                form.submit();

                setTimeout(() => {
                    Swal.fire({
                        title: 'Redirecting...',
                        text: 'Your backup has been downloaded successfully!',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '../../index.html';
                    });
                }, 1000);

                document.body.removeChild(form);
            }
            /* END BACKUP AND DROP - STARTING A NEW FOR THIS YEARS INTRAMURALS */
        </script>

        <!-- Edit Event Modal -->
        <div id="editEventModal" style="display:none;">
            <div class="modal-content">
                <h2>Edit Event</h2>
                <input type="hidden" id="edit-event-id">
                <input id="edit-event-time" class="swal2-input" placeholder="Time" type="time">
                <input id="edit-event-activity" class="swal2-input" placeholder="Activity">
                <input id="edit-event-location" class="swal2-input" placeholder="Location">
                <select id="edit-event-status" class="swal2-input" placeholder="Status">
                    <option value="Pending">Pending</option>
                    <option value="Ongoing">On-going</option>
                    <option value="Ended">Ended</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Moved">Moved</option>
                </select>
                <button id="saveEditBtn">Save Changes</button>
            </div>
        </div>
        <!-- Edit Day Modal -->
        <div id="editDayModal" style="display:none;">
            <div class="modal-content">
                <h2>Edit Day</h2>
                <input type="hidden" id="edit-day-id">
                <input id="edit-day-name" class="swal2-input" placeholder="Day Name">
                <button id="saveEditDayBtn">Save Changes</button>
            </div>
        </div>
    <?php } else { // Display message - not permitted to view
        echo '
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Oops!</strong> You lack the permission to view the \'Schedule\' features.
        </div>
    ';
    } ?>
</body>

</html>