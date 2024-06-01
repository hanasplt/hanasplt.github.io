<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

include "db-connection.php";

$input = json_decode(file_get_contents('php://input'), true);

$appt_id = $input['appt_id'];
$appt_first_name = $input['appt_first_name'];
$appt_last_name = $input['appt_last_name'];
$appt_phone_number = $input['appt_phone_number'];
$appt_email = $input['appt_email'];
$appt_brand_model = $input['appt_brand_model'];
$appt_unit_issue = $input['appt_unit_issue'];
$services_rendered = $input['services_rendered'];
$services_fee = $input['services_fee'];
$services_bill_date = $input['services_bill_date'];

$sql = "INSERT INTO services (appt_id, appt_first_name, appt_last_name, appt_phone_number, appt_email, appt_brand_model, appt_unit_issue, services_rendered, services_fee, services_bill_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ississssds", $appt_id, $appt_first_name, $appt_last_name, $appt_phone_number, $appt_email, $appt_brand_model, $appt_unit_issue, $services_rendered, $services_fee, $services_bill_date);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to insert data']);
}

$stmt->close();
$conn->close();
?>
