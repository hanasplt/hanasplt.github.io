<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include "db-connection.php";

// Fetch appointments from the database excluding confirmed ones
$sql = "SELECT * FROM appointments WHERE appt_status = 'active' ORDER BY appt_date ASC, STR_TO_DATE(SUBSTRING_INDEX(appt_time, ' - ', 1), '%h:%i %p') ASC";
$result = $conn->query($sql);

$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}
?>


<!DOCTYPE html>
<html>
    <head>
        <title>APPOINTMENTS</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="appointments.css">
        <!-- fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <!-- icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <!--Web-logo-->
        <link rel="icon" href="icons/logo.svg">
    </head>

    <body>
        <div class="menu">
            <div class="close-button">
                <i id="menuIcon" class="fas fa-times icon-white"></i>
            </div>
            
            <div class="admin">
                <h1>Welcome,</h1>
                <img src="icons/logo.png"/>
                <p><b>Admin</b></p>
            </div>

            <div class="buttons">
                <input type="button" name="dash" id="dashButton" value="Dashboard">
                <input type="button" name="app" id="appButton" value="Appointments">
                <input type="button" name="reserv" id="reservButton" value="Reservations">
                <input type="button" name="inv" id="invButton" value="Inventory">
                <input type="button" name="history" id="historyButton" value="History">
                <input type="button" name="bill" id="billButton" value="Billing">
            </div>

            <div class="logout">
                <input type="button" name="logout" id="logoutButton" value="LOG OUT">
            </div>
        </div>

        <div class="mainContent">
            <div class="rightSide" id="dash">
                <i id="menuIcon" class="fas fa-bars icon-white"></i>
                <h1>Today's Date:</h1>
                <p id="date"><?php echo date('d/m/Y'); ?></p>
            </div>

            <div class="inputs">
                <select id="date-sort">
                    <option value="recent">Recent</option>
                    <option value="upcoming">Upcoming</option>
                </select>

                <div>
                    <form id="searchContainer">
                        <button id="searchButton" type="button"><i class="fas fa-search"></i></button>
                        <div class="divider"></div>
                        <input type="text" id="searchInput" placeholder="Search...">
                    </form>
                </div>
            </div>

            <div class="content">
                <div class="UpcomingAppointments">
                    <p>Appointments</p>
                    
                    <?php foreach ($appointments as $appointment) { ?>
                        <div class="appoint">
                            <p><?php echo htmlspecialchars($appointment['appt_first_name']) . " " . htmlspecialchars($appointment['appt_last_name']) . " | " . date('d/m/Y', strtotime($appointment['appt_date'])) . " | " . htmlspecialchars($appointment['appt_time']); ?></p>
                            <a href="appointmentsFormAdmin.php?appt_id=<?php echo $appointment['appt_id']; ?>"><button name="details" id="deetsButton">Details</button></a>
                        </div>
                        <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
        
        <script src="appointments.js"></script>
    </body>
</html>
