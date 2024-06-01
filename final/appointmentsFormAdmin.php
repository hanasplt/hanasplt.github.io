<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include "db-connection.php";

if (!isset($_GET['appt_id'])) {
    echo "Appointment ID not provided.";
    exit();
}

$appt_id = $_GET['appt_id'];
$sql = "SELECT * FROM appointments WHERE appt_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Appointment not found.";
    exit();
}

$appointment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>APPOINTMENTS</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="appointmentsFormAdmin.css">
        <!-- fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <!-- icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <!--Web-logo-->
        <link rel="icon" href="icons/logo.svg">
        <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            <div class="content">
                <div class="UpcomingAppointments">
                    <p>Appointments</p>

                    <div class="appoint">
                        <div class="appoint-form">
                            <p>Appointment ID:</p>
                            <p>First Name:</p>
                            <p>Last Name:</p>
                            <p>Phone Number:</p>
                            <p>Email:</p>
                            <hr/>
                            <p>Date:</p>
                            <p>Time:</p>
                            <p>Model Unit:</p>
                            <p>Gadget Issue:</p>
                        </div>

                        <div class="appoint-deets">
                            <p><?php echo htmlspecialchars($appointment['appt_id']); ?></p>
                            <p><?php echo htmlspecialchars($appointment['appt_first_name']); ?></p>
                            <p><?php echo htmlspecialchars($appointment['appt_last_name']); ?></p>
                            <p><?php echo htmlspecialchars($appointment['appt_phone_number']); ?></p>
                            <p><?php echo htmlspecialchars($appointment['appt_email']); ?></p>
                            <hr/>
                            <p><?php echo date('F j, Y', strtotime($appointment['appt_date'])); ?></p>
                            <p><?php echo htmlspecialchars($appointment['appt_time']); ?></p>
                            <p><?php echo htmlspecialchars($appointment['appt_brand_model']); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($appointment['appt_unit_issue'])); ?></p>
                        </div>
                    </div>
                    <div class="appoint-buttons">
                        <input type="button" name="confirm" id="confirmButton" value="Confirm">
                        <input type="button" name="back" id="backButton" value="Back" onclick="window.history.back();">
                    </div>
                </div>
            </div>
        </div>

        <script src="appointmentsFormAdmin.js"></script>
    </body>
</html>
