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

// Get the prod_id from the query string
if (isset($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];

    // Fetch data from the database
    $sql = "SELECT * FROM inventory WHERE prod_id = $prod_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No product found";
        exit();
    }
} else {
    echo "No product ID specified.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
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
    <div class="formContainer">
        <a href="inventory-land.php"><input type="button" name="back" value="BACK"></a>
    </div>
    <div class="content">
        <div class="new-inventory-view">
            <p id="inventory-view-title"><?php echo $row['prod_brand']; ?></p>
            <br />
            <div class="inventory-view-content">
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">PRODUCT ID:</div>
                    <div class="inventory-view-value"><?php echo $row['prod_id']; ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">TYPE:</div>
                    <div class="inventory-view-value"><?php echo $row['prod_type']; ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">MODEL:</div>
                    <div class="inventory-view-value"><?php echo $row['prod_model']; ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">COLOR/S:</div>
                    <div class="inventory-view-value"><?php echo $row['prod_color']; ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">QUANTITY:</div>
                    <div class="inventory-view-value"><?php echo $row['prod_quantity']; ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">UNIT PRICE:</div>
                    <div class="inventory-view-value"><?php echo "PHP " . number_format($row['prod_unit_price'], 2); ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">SPECIFICATIONS:</div>
                    <div class="inventory-view-value"><?php echo $row['prod_specs']; ?></div>
                </div>
                <hr class="inventory-view-line" />
                <div class="inventory-view-content-item">
                    <div class="inventory-view-label">IMAGES:</div>
                    <div class="images-wrapper">
                        <img src="display-image.php?prod_id=<?php echo $row['prod_id']; ?>" alt="Product Image">
                    </div>
                </div>
            </div>
            <div class="button-container">
                <div class="inventory-view-button"><a href="inventory-phone-edit.html" class="btn">Edit</a></div>
                <div class="inventory-view-button"><a href="inventory-delete.php?prod_id=<?php echo $row['prod_id']; ?>" class="btn-delete">Delete</a></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="inventory-add.js"></script>
</body>
</html>
