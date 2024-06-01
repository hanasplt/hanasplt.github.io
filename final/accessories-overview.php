<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include "header.php";
include "db-connection.php";

if (!isset($_GET['prod_id'])) {
    header("Location: accessories-homepage.php");
    exit();
}

$prod_id = $_GET['prod_id'];
$sql = "SELECT prod_brand, prod_model, prod_images, prod_unit_price, prod_specs, prod_color FROM inventory WHERE prod_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prod_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit();
}

$row = $result->fetch_assoc();
$prod_model = $row['prod_model'];

// Fetch all color variations of the same model
$sql = "SELECT prod_id, prod_color FROM inventory WHERE prod_model = ? AND prod_type != 'Phone'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $prod_model);
$stmt->execute();
$colors_result = $stmt->get_result();

$colors = [];
while ($color_row = $colors_result->fetch_assoc()) {
    $colors[] = $color_row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>CHES Cellphone and Accessories Shop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--css-->
    <link href="accessories-overview.css" type="text/css" rel="stylesheet" />

    <!--fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">

    <!--icons-->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-solid-straight/css/uicons-solid-straight.css'>

    <!--Web-logo-->
    <link rel="icon" href="icons/logo.svg">
</head>
<body>
    <div style="height: 15%;"></div>

    <!--phone full specs-->
    <div class="specs-container">
        <div class="phone-container">
            <div class="back-btn"><img src="icons/back.svg"></div>
            <div class="image">
                <button class="previous-btn"><</button>
                <img width="200" src="data:image/jpeg;base64,<?php echo base64_encode($row['prod_images']); ?>" alt="<?php echo htmlspecialchars($row['prod_brand']) . ' ' . htmlspecialchars($row['prod_model']); ?>">
                <button class="next-btn">></button>
            </div>
        </div>
        <div class="specs-details">
            <div class="specs">
                <p><?php echo htmlspecialchars($row['prod_brand']) . ' ' . htmlspecialchars($row['prod_model']); ?></p><br>
                <p><b>Specifications:</b></p>
                <ul>
                    <?php
                    $specs = explode(',', $row['prod_specs']);
                    foreach ($specs as $spec) {
                        echo '<li>' . htmlspecialchars($spec) . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="other-details">
                <p class="price">Php <?php echo number_format($row['prod_unit_price'], 2); ?></p><br>
                <div class="colors">
                    <p><b>Available colors:</b></p>
                    <ul>
                        <?php
                        foreach ($colors as $color) {
                            echo '<li><a href="accessories-overview.php?prod_id=' . $color['prod_id'] . '">' . htmlspecialchars($color['prod_color']) . '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <div style="height: 16%;"></div>
                <button class="pre-order-btn" id="preOrderID">Pre Order Now</button>
            </div>
        </div>
    </div>
    <!--script-->
    <script type="text/javascript">
        let back = document.querySelector('.back-btn');

        back.onclick = () => {
            window.location.href = "accessories-homepage.php";
        }
    </script>
</body>
</html>
