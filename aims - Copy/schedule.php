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

            usort($events, function($a, $b) {
                return strcmp($a['time'], $b['time']);
            });
        }

        $row_day['events'] = $events;
        $scheduled_days[] = $row_day;
    }
}


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
                <?php $dayCounter = 1;?>
                <?php foreach ($scheduled_days as $day): ?>
                    <div class="day-header">
                        <h3>Day <?php echo $dayCounter++; ?></h3>
                    </div>
                    <div class="title-schedule">
                        <div class="header-left">
                            <h4 id="dayDate"><?php echo date('F j, Y', strtotime($day['day_date'])); ?></h4>
                        </div>
                        <div class="header-right">
                            <button id="editHeaderBtn" class="header-btn editHeaderBtn" data-day-id="<?php echo $day['id']; ?>">Edit</button>
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
                            <?php if (count($day['events']) == 0): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No events scheduled for this day.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($day['events'] as $event): ?>
                                    <tr data-event-id="<?php echo $event['id']; ?>">
                                        <td><?php echo date('h:i A', strtotime($event['time'])); ?></td>
                                        <td><?php echo $event['activity']; ?></td>
                                        <td><?php echo $event['location']; ?></td>
                                        <td><?php echo $event['status']; ?></td>
                                        <td>
                                            <button class="edit-btn" data-event-id="<?php echo $event['id']; ?>">Edit</button>
                                            <button class="delete-btn">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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

    <!-- time sorting-->
    <script>
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

    </script>

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
                                xhrAdd.onload = function () {
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
                        const time24 = document.getElementById('event-time').value;
                        const activity = document.getElementById('event-activity').value;
                        const location = document.getElementById('event-location').value;
                        const status = document.getElementById('event-status').value;

                        if (!time24 || !activity || !location) {
                            Swal.showValidationMessage('Please fill in all fields');
                            return false;
                        }

                        const [hour, minute] = time24.split(':');
                        const hours = parseInt(hour);
                        const period = hours >= 12 ? 'PM' : 'AM';
                        const hour12 = (hours % 12) || 12;
                        const time12 = `${hour12}:${minute} ${period}`;

                        return {
                            time: time12,
                            activity,
                            location,
                            status
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { time, activity, location, status } = result.value;
                        
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "add_event.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    const table = document.querySelector(`#scheduleTable-${dayId} tbody`);
                                    
                                    const noEventsRow = table.querySelector('tr td[colspan="5"]');
                                    if (noEventsRow) {
                                        noEventsRow.parentNode.removeChild(noEventsRow);
                                    }
                                    
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

                                    sortTableByTime(`scheduleTable-${dayId}`);

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

        //edit event specific to date
        document.querySelectorAll('.edit-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const cells = row.getElementsByTagName('td');
                const eventId = row.getAttribute('data-event-id');
                
                const time24 = cells[0].textContent.trim();
                const activity = cells[1].textContent.trim();
                const location = cells[2].textContent.trim();
                const status = cells[3].textContent.trim();

                let [hour, minute] = time24.split(':');
                hour = parseInt(hour, 10);
                const period = hour >= 12 ? 'PM' : 'AM';
                const hour12 = (hour % 12) || 12;
                const time12 = `${hour12.toString().padStart(2, '0')}:${minute} ${period}`;

                Swal.fire({
                    title: 'Edit Event',
                    html: `
                        <input id="edit-time" class="swal2-input" type="time" value="${time24}">
                        <input id="edit-activity" class="swal2-input" placeholder="Activity" value="${activity}">
                        <input id="edit-location" class="swal2-input" placeholder="Location" value="${location}">
                        <select id="edit-status" class="swal2-input">
                            <option value="Pending" ${status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Ongoing" ${status === 'Ongoing' ? 'selected' : ''}>On-going</option>
                            <option value="Ended" ${status === 'Ended' ? 'selected' : ''}>Ended</option>
                            <option value="Cancelled" ${status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                            <option value="Moved" ${status === 'Moved' ? 'selected' : ''}>Moved</option>
                        </select>
                    `,
                    confirmButtonText: 'Save',
                    showCancelButton: true,
                    preConfirm: () => {
                        const time24 = document.getElementById('edit-time').value;
                        const activity = document.getElementById('edit-activity').value;
                        const location = document.getElementById('edit-location').value;
                        const status = document.getElementById('edit-status').value;

                        if (!time24 || !activity || !location) {
                            Swal.showValidationMessage('Please fill in all fields');
                            return false;
                        }

                        let [hour, minute] = time24.split(':');
                        hour = parseInt(hour, 10);
                        const period = hour >= 12 ? 'PM' : 'AM';
                        const hour12 = (hour % 12) || 12;
                        const time12 = `${hour12.toString().padStart(2, '0')}:${minute} ${period}`;

                        return {
                            time24,
                            time12,
                            activity,
                            location,
                            status
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { time24, time12, activity, location, status } = result.value;

                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "edit_event.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    cells[0].textContent = time12;
                                    cells[0].setAttribute('data-time', time24);
                                    cells[1].textContent = activity;
                                    cells[2].textContent = location;
                                    cells[3].textContent = status;

                                    sortTableByTime(`scheduleTable-${dayId}`);
                                    location.reload();

                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Event updated successfully.',
                                        icon: 'success',
                                        confirmButtonColor: '#7FD278',
                                        confirmButtonText: 'OK'
                                    })
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
                        xhr.send(`event_id=${eventId}&time=${time24}&activity=${activity}&location=${location}&status=${status}`);
                    }
                });
            });
        });

        //edit date
        document.querySelectorAll('.editHeaderBtn').forEach(button => {
            button.addEventListener('click', function() {
                const dayId = this.getAttribute('data-day-id');
                
                const currentDayElement = document.getElementById(dayId);
                const currentDayDate = currentDayElement ? currentDayElement.textContent : '';
                
                Swal.fire({
                    title: 'Edit Date',
                    html: ` 
                        <input id="new-day-date" class="swal2-input" type="date" value="${currentDayDate}">
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
                        const { dayId, dayDate } = result.value;

                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "edit_day.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
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
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        };
                        xhr.send(`day_id=${dayId}&day_date=${dayDate}`);
                    }
                });
            });
        });



        function reattachEventListeners() {

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const eventId = row.getAttribute('data-event-id');

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
                        xhr.onload = function () {
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

                        // Send event ID to PHP script
                        xhr.send(`event_id=${eventId}`);
                    }
                });
            });
        });

        }

        reattachEventListeners();
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
    
    <style>
    #editEventModal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border: 1px solid #ccc;
        z-index: 1000;
    }

    .modal-content {
        padding: 20px;
    }

    .swal2-input {
        margin-bottom: 10px;
        width: 100%;
    }
    </style>


    <!-- Edit Day Modal -->
    <div id="editDayModal" style="display:none;">
        <div class="modal-content">
            <h2>Edit Day</h2>
            <input type="hidden" id="edit-day-id">
            <input id="edit-day-name" class="swal2-input" placeholder="Day Name">
            <button id="saveEditDayBtn">Save Changes</button>
        </div>
    </div>

    <style>
    #editDayModal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border: 1px solid #ccc;
        z-index: 1000;
    }
    </style>

</body>
</html>
