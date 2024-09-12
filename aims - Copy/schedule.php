<?php

include 'db.php';

// Fetch scheduled days
$query_days = "SELECT * FROM scheduled_days";
$result_days = $conn->query($query_days);

$scheduled_days = [];

if ($result_days->num_rows > 0) {
    while ($row_day = $result_days->fetch_assoc()) {
        $day_id = $row_day['id'];
        
        // Fetch events for each day
        $query_events = "SELECT * FROM scheduled_eventstoday WHERE day_id = ?";
        $stmt_events = $conn->prepare($query_events);
        $stmt_events->bind_param("i", $day_id);
        $stmt_events->execute();
        $result_events = $stmt_events->get_result();
        
        $events = [];
        if ($result_events->num_rows > 0) {
            while ($row_event = $result_events->fetch_assoc()) {
                $events[] = $row_event;
            }
        }

        $row_day['events'] = $events;
        $scheduled_days[] = $row_day;
    }
}

// Sort the array by 'day_date' in ascending order
usort($scheduled_days, function($a, $b) {
    return strtotime($a['day_date']) - strtotime($b['day_date']);
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>Schedule</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/schedule.css">
    <link rel="icon" href="assets/icons/logo.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="nav-bar">
        <img class="logo-img" src="assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>ADMINISTRATOR</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.php';">Home</p>
            <p onclick="window.location.href = 'accounts.php';">Accounts</p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';">Events</p>
            <p onclick="window.location.href = 'schedule.php';"><b>Schedule</b></p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>

    <div class="new-sched" id="openPopup">
        <div class="plus-icon">
            <i class="fas fa-plus"></i>
        </div>
        <div id="addDayBtn">
            <p id="create">Add Day</p>
            <p id="add">Add day for intramurals event.</p>
        </div>
    </div>

    <div class="schedule">
        <div class="schedule-title">
            <p id="sched">Schedule</p>
        </div>
        <div class="schedule-history">
            <?php if (count($scheduled_days) > 0): ?>
                <?php $dayCounter = 1; // Initialize day counter ?>
                <?php foreach ($scheduled_days as $day): ?>
                    <div class="day-header">
                        <h3>Day <?php echo $dayCounter++; ?></h3> <!-- Day header -->
                    </div>
                    <div class="title-schedule">
                        <div class="header-left">
                            <h4 id="dayDate"><?php echo date('F j, Y', strtotime($day['day_date'])); ?></h4>
                        </div>
                        <div class="header-right">
                            <button id="editHeaderBtn" class="header-btn" onclick="editDay(<?php echo $day['id']; ?>)">Edit</button>
                            <button id="deleteHeaderBtn" class="header-btn deleteHeaderBtn" data-day-id="<?php echo $day['id']; ?>">Delete</button>
                        </div>
                    </div>

                    <!-- Events table -->
                    <table id="scheduleTable-<?php echo $day['id']; ?>">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($day['events']) > 0): ?>
                                <?php foreach ($day['events'] as $event): ?>
                                    <tr>
                                        <td><?php echo $event['time']; ?></td>
                                        <td><?php echo $event['activity']; ?></td>
                                        <td><?php echo $event['location']; ?></td>
                                        <td><?php echo $event['status']; ?></td>
                                        <td>
                                            <button class="edit-btn">Edit</button>
                                            <button class="delete-btn">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No events scheduled for this day.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                    <button class="addEventBtn" id="addEventBtn" data-day-id="<?php echo $day['id']; ?>">Add Event</button>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No schedule available. Please add new days.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- logout confirmation -->
    <script>
        document.getElementById('logoutIcon').addEventListener('click', function() {
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
                    window.location.href = 'index.html';
                }
            });
        });
    </script>

    <!-- Add day popup -->
    <script>
        document.getElementById('openPopup').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Day',
                html: ` 
                    <input id="new-day-date" class="swal2-input" placeholder="Date" type="date">
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
                    const { dayDate } = result.value;

                    const xhr = new XMLHttpRequest();
                        xhr.open("POST", "add_day.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'New day added successfully.',
                                        icon: 'success',
                                        confirmButtonColor: '#7FD278',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload(); // Reload the page to reflect new data
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
                        xhr.send(`day_date=${dayDate}`);
                }
            });
        });

        // Delete date
        document.querySelectorAll('.deleteHeaderBtn').forEach(button => {
            button.addEventListener('click', function () {
                const dayId = this.getAttribute('data-day-id');

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

                        xhr.onload = function () {
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
                                        location.reload();
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

                        xhr.send(`day_id=${dayId}`);
                    }
                });
            });
        });

        // Add event to specific date
        document.querySelectorAll('.addEventBtn').forEach(function (button) {
            button.addEventListener('click', function() {
                const dayId = button.getAttribute('data-day-id');
                
                Swal.fire({
                    title: 'Add New Event',
                    html: `
                        <input id="event-time" class="swal2-input" placeholder="Time" type="time">
                        <input id="event-activity" class="swal2-input" placeholder="Activity">
                        <input id="event-location" class="swal2-input" placeholder="Location">
                        <select id="event-status" class="swal2-input">
                            <option value="Pending">Pending</option>
                            <option value="Ongoing">On-going</option>
                            <option value="Ended">Ended</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Moved">Moved</option>
                        </select>
                    `,
                    confirmButtonText: 'Add',
                    showCancelButton: true,
                    preConfirm: () => {
                        const time = document.getElementById('event-time').value;
                        const activity = document.getElementById('event-activity').value;
                        const location = document.getElementById('event-location').value;
                        const status = document.getElementById('event-status').value;

                        if (!time || !activity || !location) {
                            Swal.showValidationMessage('Please fill in all fields');
                            return false;
                        }

                        return {
                            time,
                            activity,
                            location,
                            status
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { time, activity, location, status } = result.value;
                        
                        // Send the event data to the server
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "add_event.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    // Append the new row to the specific day's table
                                    const table = document.querySelector(`#scheduleTable-${dayId} tbody`);
                                    const newRow = table.insertRow();
                                    
                                    newRow.innerHTML = `
                                        <td>${time}</td>
                                        <td>${activity}</td>
                                        <td>${location}</td>
                                        <td>${status}</td>
                                        <td>
                                            <button class="edit-btn">Edit</button>
                                            <button class="delete-btn">Delete</button>
                                        </td>
                                    `;

                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Event added successfully.',
                                        icon: 'success',
                                        confirmButtonColor: '#7FD278',
                                        confirmButtonText: 'OK'
                                    });

                                    reattachEventListeners();
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
                        xhr.send(`day_id=${dayId}&time=${time}&activity=${activity}&location=${location}&status=${status}`);
                    }
                });
            });
        });

        function reattachEventListeners() {
            // Reattach event listeners to new buttons
            document.querySelectorAll('.edit-btn').forEach(button => {
                // Add edit functionality
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                // Add delete functionality
            });
        }
    </script>
</body>
</html>
