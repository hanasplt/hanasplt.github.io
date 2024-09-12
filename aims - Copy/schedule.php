<?php
// Include your database connection
include 'db.php'; // Adjust the path as necessary

// Fetch scheduled days from the database
$query = "SELECT * FROM scheduled_days ORDER BY day_number ASC";
$result = $conn->query($query);

// Prepare an array to store scheduled days data
$scheduled_days = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $scheduled_days[] = $row;
    }
}
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
                <?php foreach ($scheduled_days as $day): ?>
                    <div class="title-schedule">
                        <div class="header-left">
                            <h3 id="dayName">Day <?php echo $day['day_number']; ?></h3>
                            <h4 id="dayDate"><?php echo date('F j, Y', strtotime($day['day_date'])); ?></h4>
                        </div>
                        <div class="header-right">
                            <button id="editHeaderBtn" class="header-btn" onclick="editDay(<?php echo $day['id']; ?>)">Edit</button>
                            <button id="deleteHeaderBtn" class="header-btn" onclick="deleteDay(<?php echo $day['id']; ?>)">Delete</button>
                        </div>
                    </div>

                    <!-- Empty table structure for now -->
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
                                <td colspan="5" style="text-align: center;">No events scheduled for this day.</td>
                            </tr>
                        </tbody>
                    </table>
                    <button id="addEventBtn" onclick="addEvent(<?php echo $day['id']; ?>)">Add Event</button>
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

                    // Send data to the PHP script via AJAX
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
                                });
                                location.reload(); // Reload the page to reflect the new day
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
                    xhr.send(`day_number=${dayNumber}&day_date=${dayDate}`);
                }
            });
        });
    </script>
</body>
</html>
