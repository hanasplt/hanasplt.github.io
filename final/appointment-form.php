<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include "db-connection.php";

$sql = "SELECT * FROM client_accounts WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $first_name = htmlspecialchars($row['first_name']);
    $last_name = htmlspecialchars($row['last_name']);
    $phone_number = htmlspecialchars($row['phone_number']);
    $email = htmlspecialchars($row['email']);
} else {
    echo "User not found!";
    exit();
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
    // Retrieve form data
    $appt_first_name = htmlspecialchars($_POST['appt_first_name']);
    $appt_last_name = htmlspecialchars($_POST['appt_last_name']);
    $appt_phone_number = htmlspecialchars($_POST['appt_phone_number']);
    $appt_email = htmlspecialchars($_POST['appt_email']);
    $appt_brand_model = htmlspecialchars($_POST['appt_brand_model']);
    $appt_unit_issue = htmlspecialchars($_POST['appt_unit_issue']);
    $appt_date = htmlspecialchars($_POST['appt_date']);
    $appt_time = htmlspecialchars($_POST['appt_time']);

    $sql_insert = "INSERT INTO appointments (appt_first_name, appt_last_name, appt_phone_number, appt_email, appt_brand_model, appt_unit_issue, appt_date, appt_time)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssssss", $appt_first_name, $appt_last_name, $appt_phone_number, $appt_email, $appt_brand_model, $appt_unit_issue, $appt_date, $appt_time);

    if ($stmt_insert->execute()) {
        $success = true;
    } else {
        echo "Error inserting record: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>CHES Cellphone and Accessories Shop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--css-->
    <link href="appointment-form.css" type="text/css" rel="stylesheet" />
    <!---->
    <!--fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
    <!---->
    <!--icons-->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <!---->
    <!--Web-logo-->
    <link rel="icon" href="icons/logo.svg">
    <!---->
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!---->
</head>
<body>
    <!--header-->
    <div class="left-header">
        <img src="icons/logo.svg">
        <p>CHES</p>
    </div>
    <!---->
    <!--main container-->
    <div class="main-container">
        <div class="left-container">
            <img src="icons/repair-icon.svg">
            <p>Book now for device repairs at CHES Cellphone and Accessories Shop</p>
        </div>
        <div class="right-container">
            <div class="title-container">
                <div class="line"></div>
                <b>Appointment Form</b><br>
                <p>You may cancel your appointment on the same day it is scheduled.</p>
            </div>
            <form method="post">
                <div class="form-container">
                    <div class="info-container">
                        <div>
                            <p>First Name</p>
                            <div class="displayInfo"><span><?php echo $first_name; ?></span></div>
                            <input type="hidden" name="appt_first_name" value="<?php echo $first_name; ?>">
                            <p>Phone Number</p>
                            <div class="displayInfo"><span>0<?php echo $phone_number; ?></span></div><br>
                            <input type="hidden" name="appt_phone_number" value="0<?php echo $phone_number; ?>">
                        </div>
                        <div class="displayInfo2">
                            <p>Last Name</p>
                            <div class="displayInfo"><span><?php echo $last_name; ?></span></div>
                            <input type="hidden" name="appt_last_name" value="<?php echo $last_name; ?>">
                            <p>Email</p>
                            <div class="displayInfo"><span><?php echo $email; ?></span></div>
                            <input type="hidden" name="appt_email" value="<?php echo $email; ?>">
                        </div>
                    </div>
                    <i>Fill out the following:</i><br><br>
                    <div class="fill-out-container">
                        <div>
                            <p>Brand & Model Unit</p>
                            <input type="text" name="appt_brand_model" id="cpUnitID" class="cpUnit" required>
                            <p>Unit Issue</p>
                            <textarea id="unitIssueID" name="appt_unit_issue" class="unitIssue"></textarea><br><br><br>
                        </div>
                        <div class="inputInfo">
                            <label for="appointmentDate">Date:</label><br>
                            <input type="date" name="appt_date" id="date" class="appointmentDate" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" max="<?php echo date('Y-m-d', strtotime('+3 month')); ?>"  onchange="fetchAvailableTimeSlots()" required><br>
                            <label for="appointment-time">Time</label><br>
                            <select name="appt_time" id="timeID" class="time" required>
                                <option>9:00 A.M. - 12:00 P.M.</option>
                                <option>1:00 P.M. - 4:00 P.M.</option>
                                <option>4:00 P.M. - 7:00 P.M.</option>
                            </select><br>
                        </div>
                    </div>
                    <div class="button-container">
                        <button type="button" class="cancel-btn" id="cancelID">CANCEL</button>
                        <button type="submit" class="confirm-btn" id="confirmID" name="confirm">CONFIRM</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--script-->
    <script type="text/javascript">
        let backbtn = document.getElementById('cancelID');

        backbtn.onclick = () => {
            window.location.href = "services-homepage.php";
        }
        function fetchAvailableTimeSlots() {
            var selectedDate = document.getElementById("date").value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch-available-time-slots.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var availableTimeSlots = JSON.parse(xhr.responseText);
                    var timeSelect = document.getElementById("timeID");
                    timeSelect.innerHTML = "";
                    if (availableTimeSlots.length === 0) {
                        var option = document.createElement("option");
                        option.text = "No time slots available";
                        timeSelect.add(option);
                    } else {
                        availableTimeSlots.forEach(function(timeSlot) {
                            var option = document.createElement("option");
                            option.text = timeSlot;
                            timeSelect.add(option);
                        });
                    }
                }
            };
            xhr.send("selected_date=" + selectedDate);
        }
    </script>
    <!-- SweetAlert2 -->
    <?php if ($success): ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Your appointment date will be set.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F79256',
                    cancelButtonColor: '#2A5181',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Appointment Date Set!',
                            text: 'Please come to the store in the date set.',
                            icon: 'success'
                        }).then(() => {
                            window.location.href = 'appointment-form.html';
                        });
                    }
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>