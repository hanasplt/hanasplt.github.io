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

// Get prod_id from query string
if(isset($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];

    // Prepare SQL statement to fetch image data
    $sql = "SELECT prod_images FROM inventory WHERE prod_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $stmt->bind_result($imageData);
    $stmt->fetch();
    $stmt->close();

    // Output the image
    header("Content-type: image/jpeg");
    echo $imageData;
} else {
    echo "No image ID specified.";
}

$conn->close();
?>
