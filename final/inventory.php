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

$imagePath = ""; // Variable to store the image path

// Check if file was uploaded successfully
if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $tmpName = $_FILES['image']['tmp_name'];

    $validImageExtension = ['jpg', 'jpeg', 'png'];
    $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($imageExtension, $validImageExtension)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Extension',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
        exit();
    } else if ($fileSize > 1000000) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Image Size Is Too Large',
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>";
        exit();
    } else {
        $newImageName = uniqid() . '.' . $imageExtension;
        $imagePath = 'images/' . $newImageName;
        move_uploaded_file($tmpName, $imagePath);
    }
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'File upload failed or no file was selected',
                showConfirmButton: false,
                timer: 1500
            });
          </script>";
    exit();
}

// Insert data into the database
$sql = "INSERT INTO inventory (prod_type, prod_brand, prod_model, prod_color, prod_quantity, prod_unit_price, prod_specs, prod_images) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisdss", $type, $brand, $model, $color, $quantity, $price, $specs, $imagePath);

if ($stmt->execute()) {
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
    exit(); // Ensure that script execution stops after displaying the alert
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
</body>
</html>
