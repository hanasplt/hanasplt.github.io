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
if (isset($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];

    // Prepare SQL statement to fetch image data
    $sql = "SELECT prod_images FROM inventory WHERE prod_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($imageData);
    $stmt->fetch();

    // Check if image data is retrieved
    if ($stmt->num_rows > 0) {
        // Output the image with the correct MIME type
        header("Content-Type: image/jpg"); // Adjust this based on the actual image type stored
        echo $imageData;
    } else {
        echo "No image found for the specified ID.";
    }

    $stmt->close();
} else {
    echo "No image ID specified.";
}

$conn->close();
?>
