<?php
header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chestechshopdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("error" => "Connection failed: " . $conn->connect_error)));
}

// Fetch counts
$counts = array(
    "appointments" => 0,
    "reservations" => 0,
    "inventory" => 0
);

// Appointments count
$sql = "SELECT COUNT(*) as count FROM appointments";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $counts["appointments"] = $row['count'];
}

// Reservations count
$sql = "SELECT COUNT(*) as count FROM reservations";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $counts["reservations"] = $row['count'];
}

// Inventory total quantity
$sql = "SELECT SUM(prod_quantity) as total_quantity FROM inventory";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $counts["inventory"] = $row['total_quantity'];
}

$conn->close();

// Output counts as JSON
echo json_encode($counts);
?>
