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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prod_id = $_POST['prod_id'];
    $model = $_POST['model'];
    $color = $_POST['color'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $specs = $_POST['specs'];

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageType = $_FILES['image']['type'];

        // Update product with image
        $sql = "UPDATE inventory SET prod_model=?, prod_color=?, prod_quantity=?, prod_unit_price=?, prod_specs=?, prod_images=?, image_type=? WHERE prod_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidsbsi", $model, $color, $quantity, $price, $specs, $imageData, $imageType, $prod_id);
    } else {
        // Update product without image
        $sql = "UPDATE inventory SET prod_model=?, prod_color=?, prod_quantity=?, prod_unit_price=?, prod_specs=? WHERE prod_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidsi", $model, $color, $quantity, $price, $specs, $prod_id);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully.";
        header("Location: inventory-land.php");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
