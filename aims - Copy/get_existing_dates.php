<?php

include 'encryption.php';

$host = 'localhost'; 
$username = 'root';  
$password = '';   
$dbname = 'ilps'; 

$conn = new mysqli($host, $username, $password, $dbname);

$query = "SELECT day_date FROM scheduled_days";
$result = mysqli_query($conn, $query);

$dates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $dates[] = $row['day_date'];
}

echo json_encode($dates);
?>