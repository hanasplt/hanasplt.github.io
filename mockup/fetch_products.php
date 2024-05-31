<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chestechshopdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT prod_brand, prod_model, prod_images, prod_unit_price FROM products";
$result = $conn->query($sql);

$products = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} 

$conn->close();

header('Content-Type: application/json');
echo json_encode($products);
?>
