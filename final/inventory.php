<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
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

// Retrieve form data
$type = $_POST['type'];
$brand = $_POST['brand'];
$model = $_POST['model'];
$color = $_POST['color'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];
$specs = $_POST['specs'];

// Check if file was uploaded successfully
if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image = $_FILES['image']['tmp_name']; // Temporary file path
    // Check if file path is not empty
    if($image != "") {
        // Prepare image data
        $imageData = addslashes(file_get_contents($image)); // Convert image to binary data for storage in the database
    } else {
        // Handle case where file path is empty
        echo "Error: Uploaded file path is empty.";
        exit();
    }
} else {
    // Handle case where file was not uploaded successfully
    echo "Error: File upload failed or no file was selected.";
    exit();
}

// Insert data into the database
$sql = "INSERT INTO inventory (prod_type, prod_brand, prod_model, prod_color, prod_quantity, prod_unit_price, prod_specs, prod_images) VALUES ('$type', '$brand', '$model', '$color', '$quantity', '$price', '$specs', '$imageData')";

if ($conn->query($sql) === TRUE) {
    // Record inserted successfully, display SweetAlert and then redirect
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Record created successfully',
                showConfirmButton: false,
                timer: 1500 // Time in milliseconds (1.5 seconds)
            }).then(() => {
                window.location.href = 'inventory-add.html'; // Redirect after SweetAlert is closed
            });
          </script>";
    exit(); // Ensure that script execution stops after dis playing the alert
} else {
    // If there's an error with the SQL query
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
</body>
</html>
