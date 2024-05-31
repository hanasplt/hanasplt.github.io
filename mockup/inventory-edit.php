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

$prod_id = $_GET['prod_id'] ?? null;
$product = null;

if ($prod_id) {
    $sql = "SELECT * FROM inventory WHERE prod_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "No product found.";
        exit();
    }

    $stmt->close();
} else {
    echo "No product ID specified.";
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory.css">
    <!-- fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!--Web-logo-->
    <link rel="icon" href="icons/logo.svg">
</head>
<body>
<div class="menu">
    <div class="close-button">
        <i id="menuIcon" class="fas fa-times icon-white"></i>
    </div>
    <div class="admin">
        <h1>Welcome,</h1>
        <img src="icons/logo.png"/>
        <p><b>Admin</b></p>
    </div>
    <div class="buttons">
        <input type="button" name="dash" id="dashButton" value="Dashboard">
        <input type="button" name="app" id="appButton" value="Appointments">
        <input type="button" name="reserv" id="reservButton" value="Reservations">
        <input type="button" name="inv" id="invButton" value="Inventory">
        <input type="button" name="history" id="historyButton" value="History">
        <input type="button" name="bill" id="billButton" value="Billing">
    </div>
    <div class="logout">
        <input type="button" name="logout" id="logoutButton" value="LOG OUT">
    </div>
</div>
<div class="mainContent">
    <div class="rightSide" id="dash">
        <i id="menuIcon" class="fas fa-bars icon-white"></i>
        <h1>Today's Date:</h1>
        <p id="date">Date Placeholder</p>
    </div>
    <div class="content">
        <div class="inventory-aed">
            <p id="inventory-aed-title">Edit Phone</p>
            <br />
            <form action="update-product.php" method="post" enctype="multipart/form-data">
                <div class="inventory-aed-content">
                    <input type="hidden" name="prod_id" value="<?php echo htmlspecialchars($product['prod_id']); ?>">
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">TYPE:</div>
                        <div class="inventory-aed-value1">
                            <input type="text" id="dropdown4" name="type" value="<?php echo htmlspecialchars($product['prod_type']); ?>" readonly>
                        </div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">PROD. ID:</div>
                        <div class="inventory-aed-value2"><input type="text" name="prodid" value="<?php echo htmlspecialchars($product['prod_id']); ?>" readonly></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">BRAND:</div>
                        <div class="inventory-aed-value2"><input type="text" name="brand" value="<?php echo htmlspecialchars($product['prod_brand']); ?>" readonly></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">MODEL:</div>
                        <div class="inventory-aed-value2"><input type="text" name="model" value="<?php echo htmlspecialchars($product['prod_model']); ?>" readonly></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">COLOR/S:</div>
                        <div class="inventory-aed-value2"><input type="text" name="color" value="<?php echo htmlspecialchars($product['prod_color']); ?>" readonly></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">QUANTITY:</div>
                        <div class="inventory-aed-value2"><input type="text" name="quantity" value="<?php echo htmlspecialchars($product['prod_quantity']); ?>"></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">PRICE:</div>
                        <div class="inventory-aed-value2"><input type="text" name="price" value="<?php echo htmlspecialchars($product['prod_unit_price']); ?>"></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">SPECS:</div>
                        <div class="inventory-aed-value2"><input type="text" name="specs" value="<?php echo htmlspecialchars($product['prod_specs']); ?>" readonly></div>
                    </div>
                    <div class="inventory-aed-content-item">
                        <div class="inventory-aed-label">IMAGE:</div>
                        <div class="inventory-aed-value2">
                            <img src="display-image.php?prod_id=<?php echo htmlspecialchars($product['prod_id']); ?>" alt="Product Image" style="width: 200px; height: auto;" >
                        </div>
                    </div>
                </div>
                <div class="button-container1">
                    <div class="inventory-view-button"><button type="submit" class="btn btn-save">Save</button></div>
                    <div class="inventory-view-button"><a href="inventory-land.php" class="btn-cancel">Cancel</a></div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="inventory-edit-delete.js"></script>
</body>
</html>
