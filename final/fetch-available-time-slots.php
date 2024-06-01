<?php
// Include your database connection file
include "db-connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_date'])) {
    $selected_date = $_POST['selected_date'];

    // Query to fetch all time slots
    // Define your time slots array
$all_time_slots = array("9:00 A.M. - 12:00 P.M.", "1:00 P.M. - 4:00 P.M.", "4:00 P.M. - 7:00 P.M.");

// Initialize an array to store available time slots
$available_time_slots = array();

// Check each time slot for availability
foreach ($all_time_slots as $time_slot) {
    $sql_check_time_slot = "SELECT COUNT(*) AS num_appointments FROM appointments WHERE appt_date = ? AND appt_time = ?";
    $stmt_check_time_slot = $conn->prepare($sql_check_time_slot);
    $stmt_check_time_slot->bind_param("ss", $selected_date, $time_slot); // Corrected binding parameters
    $stmt_check_time_slot->execute();
    $result_check_time_slot = $stmt_check_time_slot->get_result();
    $row_check_time_slot = $result_check_time_slot->fetch_assoc();
    $num_appointments = $row_check_time_slot['num_appointments'];

    if ($num_appointments == 0) {
        // If no appointments are scheduled for this time slot, add it to the available time slots array
        $available_time_slots[] = $time_slot;
    }
}

    // Return the available time slots as JSON
    echo json_encode($available_time_slots);

    // Close the database connection and statements
    $stmt_check_time_slot->close();
    $conn->close();
}
?>
