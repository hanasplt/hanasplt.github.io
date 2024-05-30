<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "sql305.infinityfree.com";
    $username = "if0_36537149";
    $password = "chestechshop";
    $dbname = "if0_36537149_techshopdb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize input data
    $first_name = 'Debbie'; // Placeholder, replace with actual session or input data
    $last_name = 'Gerodias'; // Placeholder, replace with actual session or input data
    $phone = '09923316087'; // Placeholder, replace with actual session or input data
    $email = 'debbiegerodias19@gmail.com'; // Placeholder, replace with actual session or input data
    $cp_unit = $conn->real_escape_string($_POST['cpUnit']);
    $unit_issue = $conn->real_escape_string($_POST['unitIssue']);
    $appointment_date = $conn->real_escape_string($_POST['appointmentDate']);
    $time = $conn->real_escape_string($_POST['time']);

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO appointments (first_name, last_name, phone, email, cp_unit, unit_issue, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $last_name, $phone, $email, $cp_unit, $unit_issue, $appointment_date, $time);

    if ($stmt->execute()) {
        echo "<script>
                alert('Appointment successfully booked!');
                window.location.href = 'appointment-receipt.html';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
