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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
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
            <div class="title-schedule">
                <div class="header-left">
                    <h3 id="dayName">Day 1</h3>
                    <h4 id="dayDate">September 19, 2024</h4>
                </div>
                <div class="header-right">
                    <button id="editHeaderBtn" class="header-btn">Edit</button>
                    <button id="deleteHeaderBtn" class="header-btn">Delete</button>
                </div>
            </div>
            <table id="scheduleTable">
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
                    <tr>
                        <td>7:00 am</td>
                        <td>200m Dash</td>
                        <td>Open Field</td>
                        <td>Done</td>
                        <td>
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>8:00 am</td>
                        <td>Badminton Singles</td>
                        <td>PECC Gym</td>
                        <td>Not Done</td>
                        <td>
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button id="addEventBtn">Add Event</button>
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
                    // mag redirect siya to the login page
                    window.location.href = 'index.html';
                }
            });
        });
    </script>

    <script>
        document.getElementById('addDayBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Day',
                html: `
                    <input id="new-day-number" class="swal2-input" placeholder="Day Number" type="number" min="1">
                    <input id="new-day-date" class="swal2-input" placeholder="Date" type="date">
                `,
                confirmButtonText: 'Add',
                showCancelButton: true,
                preConfirm: () => {
                    const dayNumber = document.getElementById('new-day-number').value;
                    const dayDate = document.getElementById('new-day-date').value;
                    const today = new Date().toISOString().split('T')[0];

                    if (!dayNumber || !dayDate) {
                        Swal.showValidationMessage('Please enter both Day Number and Date');
                        return false;
                    }

                    if (dayDate < today) {
                        Swal.showValidationMessage('Date cannot be in the past');
                        return false;
                    }

                    return {
                        dayNumber: dayNumber,
                        dayDate: dayDate
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { dayNumber, dayDate } = result.value;
                    const formattedDate = formatDate(dayDate);
                    document.getElementById('dayName').textContent = `Day ${dayNumber}`;
                    document.getElementById('dayDate').textContent = formattedDate;

                    Swal.fire({
                        title: 'Success!',
                        text: 'New day added successfully.',
                        icon: 'success',
                        confirmButtonColor: '#7FD278',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        document.getElementById('editHeaderBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Edit Header',
                html: `
                    <input id="edit-day-number" class="swal2-input" placeholder="Day Number" type="number" min="1" value="${document.getElementById('dayName').textContent.replace('Day ', '')}">
                    <input id="edit-day-date" class="swal2-input" placeholder="Date" type="date" value="${document.getElementById('dayDate').textContent}">
                `,
                confirmButtonText: 'Save',
                showCancelButton: true,
                preConfirm: () => {
                    const dayNumber = document.getElementById('edit-day-number').value;
                    const dayDate = document.getElementById('edit-day-date').value;
                    const today = new Date().toISOString().split('T')[0];

                    if (!dayNumber || !dayDate) {
                        Swal.showValidationMessage('Please enter both Day Number and Date');
                        return false;
                    }

                    if (dayDate < today) {
                        Swal.showValidationMessage('Date cannot be in the past');
                        return false;
                    }

                    return {
                        dayNumber: dayNumber,
                        dayDate: dayDate
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { dayNumber, dayDate } = result.value;
                    const formattedDate = formatDate(dayDate);
                    document.getElementById('dayName').textContent = `Day ${dayNumber}`;
                    document.getElementById('dayDate').textContent = formattedDate;

                    Swal.fire({
                        title: 'Success!',
                        text: 'Header updated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#7FD278',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        document.getElementById('deleteHeaderBtn').addEventListener('click', function() {
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
                    // clear the day and date values
                    document.getElementById('dayName').textContent = 'Day';
                    document.getElementById('dayDate').textContent = '';

                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Schedule has been deleted.',
                        icon: 'success',
                        confirmButtonColor: '#7FD278',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        document.getElementById('addEventBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Event',
                html: `
                    <input id="event-time" class="swal2-input" placeholder="Time">
                    <input id="event-activity" class="swal2-input" placeholder="Activity">
                    <input id="event-location" class="swal2-input" placeholder="Location">
                    <select id="event-status" class="swal2-input">
                        <option value="Not Done">Not Done</option>
                        <option value="Done">Done</option>
                    </select>
                `,
                confirmButtonText: 'Add',
                showCancelButton: true,
                preConfirm: () => {
                    return {
                        time: document.getElementById('event-time').value,
                        activity: document.getElementById('event-activity').value,
                        location: document.getElementById('event-location').value,
                        status: document.getElementById('event-status').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { time, activity, location, status } = result.value;
                    const table = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
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
                    
                    reattachEventListeners();

                    Swal.fire({
                        title: 'Success!',
                        text: 'Event added successfully.',
                        icon: 'success',
                        confirmButtonColor: '#7FD278',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        function reattachEventListeners() {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const cells = row.getElementsByTagName('td');

                    Swal.fire({
                        title: 'Edit Event',
                        html: `
                            <input id="edit-time" class="swal2-input" placeholder="Time" value="${cells[0].textContent}">
                            <input id="edit-activity" class="swal2-input" placeholder="Activity" value="${cells[1].textContent}">
                            <input id="edit-location" class="swal2-input" placeholder="Location" value="${cells[2].textContent}">
                            <select id="edit-status" class="swal2-input">
                                <option value="Not Done" ${cells[3].textContent === 'Not Done' ? 'selected' : ''}>Not Done</option>
                                <option value="Done" ${cells[3].textContent === 'Done' ? 'selected' : ''}>Done</option>
                            </select>
                        `,
                        confirmButtonText: 'Save',
                        showCancelButton: true,
                        preConfirm: () => {
                            return {
                                time: document.getElementById('edit-time').value,
                                activity: document.getElementById('edit-activity').value,
                                location: document.getElementById('edit-location').value,
                                status: document.getElementById('edit-status').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const { time, activity, location, status } = result.value;
                            cells[0].textContent = time;
                            cells[1].textContent = activity;
                            cells[2].textContent = location;
                            cells[3].textContent = status;

                            Swal.fire({
                                title: 'Success!',
                                text: 'Event updated successfully.',
                                icon: 'success',
                                confirmButtonColor: '#7FD278',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    
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
                            row.remove();

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Your event has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#7FD278',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
            });
        }

        reattachEventListeners();

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
    </script>
</body>
</html>
