<?php

include 'encryption.php';

$host = 'localhost'; 
$username = 'root';  
$password = '';   
$dbname = 'ilps'; 

$conn = new mysqli($host, $username, $password, $dbname);

// Check if connection is successful
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if day_date is set in the POST request
if (!isset($_POST['day_date'])) {
    die(json_encode(['success' => false, 'message' => 'Day date is not provided.']));
}

$day_date = $_POST['day_date'];

// Prepare the SQL statement
$sql = "INSERT INTO scheduled_days (day_date) VALUES (?)";
$stmt = $conn->prepare($sql);

// Bind the day_date as a string ("s" for string)
$stmt->bind_param("s", $day_date);

// Execute the statement and check for success
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding new day: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
