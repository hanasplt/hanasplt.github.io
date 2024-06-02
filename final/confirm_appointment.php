<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include "db-connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are present
    $requiredFields = ['appt_id', 'appt_first_name', 'appt_last_name', 'appt_phone_number', 'appt_email', 'appt_brand_model', 'appt_unit_issue', 'services_rendered', 'services_fee', 'services_bill_date'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
            exit();
        }
    }

    // Extract form data
    $appt_id = $_POST['appt_id'];
    $appt_first_name = $_POST['appt_first_name'];
    $appt_last_name = $_POST['appt_last_name'];
    $appt_phone_number = $_POST['appt_phone_number'];
    $appt_email = $_POST['appt_email'];
    $appt_brand_model = $_POST['appt_brand_model'];
    $appt_unit_issue = $_POST['appt_unit_issue'];
    $services_rendered = $_POST['services_rendered'];
    $services_fee = $_POST['services_fee'];
    $services_bill_date = $_POST['services_bill_date'];

    // Prepare and execute the SQL query to insert service data
    $sqlInsert = "INSERT INTO services (appt_id, appt_first_name, appt_last_name, appt_phone_number, appt_email, appt_brand_model, appt_unit_issue, services_rendered, services_fee, services_bill_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ississssds", $appt_id, $appt_first_name, $appt_last_name, $appt_phone_number, $appt_email, $appt_brand_model, $appt_unit_issue, $services_rendered, $services_fee, $services_bill_date);

    if ($stmtInsert->execute()) {
        // Update the appointment status to confirmed and set the confirmed_at time
        $sqlUpdate = "UPDATE appointments SET appt_status = 'confirmed', confirmed_at = NOW() WHERE appt_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $appt_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    
        // Show SweetAlert
        echo "<script>
                Swal.fire({
                    title: 'Confirmed!',
                    text: 'The appointment has been confirmed.',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'appointments.php';
                });
              </script>";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert data']);
    }

    $stmtInsert->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>

</body>
</html>
