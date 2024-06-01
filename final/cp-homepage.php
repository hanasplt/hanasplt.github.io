    <?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: sign-in.php");
        exit();
    }

    $username = $_SESSION['username'];
    include "header.php";
    include "db-connection.php";
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>CHES Cellphone and Accessories Shop</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--css-->
        <link href="cp-homepage.css" type="text/css" rel="stylesheet" />
        <!---->

        <!--fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">
        <!---->

        <!--icons-->
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-solid-straight/css/uicons-solid-straight.css'>
        <!---->

        <!-- Web logo-->
        <link rel="icon" href="icons/logo.svg">

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <form method="post">
            <div class="main-container">
                <div class="top-container">
                    <div>
                        <select class="dropdown" id="brandDropdown" name="brandDropdown" onchange="this.form.submit()">
                            <option value="" selected>All Brands</option>
                            <?php
                            $sql = "SELECT DISTINCT prod_brand FROM inventory WHERE prod_type = 'Phone'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = (isset($_POST['brandDropdown']) && $_POST['brandDropdown'] == $row['prod_brand']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($row['prod_brand']) . '" ' . $selected . '>' . htmlspecialchars($row['prod_brand']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="search">
                        <div class="search-container">
                            <input type="text" name="search-text" class="search-input" value="<?php echo isset($_POST['search-text']) ? htmlspecialchars($_POST['search-text']) : ''; ?>">
                            <button class="search-btn" name="search"><b>Search</b><img class="search-icon" src="icons/search.svg"></button>
                        </div>
                    </div>
                </div>

                <div class="bottom-container" id="newArrivalsContainer">
                    <div class="bottom-top-container">
                        <p class="cp-title">Phones</p>
                        <div class="bottom-top-right-container">
                            <p class="new-arrivals" id="newArrivals">New Arrivals</p>
                            <p class="best-seller" id="bestSeller">Best Seller</p>
                        </div>
                    </div>
                    <div class="container">
                        <?php
                        $brandFilter = isset($_POST['brandDropdown']) ? $_POST['brandDropdown'] : '';
                        $searchText = isset($_POST['search-text']) ? $_POST['search-text'] : '';

                        if (isset($_POST['search'])) {
                            $brandFilter = ''; // Reset brand filter to "All Brands" when search is executed
                        }

                        $sql = "SELECT prod_id, prod_brand, prod_model, prod_images, prod_unit_price, prod_specs FROM inventory WHERE prod_type = 'Phone'";

                        $conditions = [];
                        if ($brandFilter !== '') {
                            $conditions[] = "prod_brand = '" . $conn->real_escape_string($brandFilter) . "'";
                        }
                        if ($searchText !== '') {
                            $conditions[] = "(prod_brand LIKE '%" . $conn->real_escape_string($searchText) . "%' OR prod_model LIKE '%" . $conn->real_escape_string($searchText). "%' OR CONCAT(prod_brand, ' ', prod_model) LIKE '%" . $conn->real_escape_string($searchText) . "%' OR prod_specs LIKE '%" . $conn->real_escape_string($searchText) . "%')";
                        }
                        if (!empty($conditions)) {
                            $sql .= " AND " . implode(" AND ", $conditions);
                        }

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo '<div class="container2">';
                                echo '<div class="cp-container">';
                                echo '<div class="left-cp-container">';
                                echo '<span name="cp-model-title" class="cp-model-title">' . htmlspecialchars($row["prod_brand"]) . ' ' . htmlspecialchars($row["prod_model"]) . '</span>';
                                echo '<div class="specifications-container">';
                                echo '<ul name="specifications">';
                                echo '<li>' . htmlspecialchars($row["prod_specs"]) . '</li>';
                                echo '</ul><br>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="right-left-container">';
                                echo '<span name="price" class="price">Php ' . number_format($row["prod_unit_price"], 2) . '</span><br>';
                                echo '<img width="150" src="data:image/jpeg;base64,' . base64_encode($row["prod_images"]) . '" alt="' . htmlspecialchars($row["prod_brand"]) . ' ' . htmlspecialchars($row["prod_model"]) . '">';
                                echo '</div>';
                                echo '</div>';
                                echo '<a href="cp-overview.php?prod_id=' . $row["prod_id"] . '">See full specs</a><br><br>';
                                echo '<button class="pre-order-btn">Pre Order Now</button>';
                                echo '</div>';
                            }
                        } else {
                            echo "No results found.";
                        }
                        ?>
                    </div>
                </div>

                <!--hidden//best-seller-->
                <div class="bottom-container" id="bestSellerContainer" style="display:none;">
                    <div class="bottom-top-container">
                        <p class="cp-title">Best Sellers</p>
                        <div class="bottom-top-right-container">
                            <p class="best-seller" id="newArrivals2">New Arrivals</p>
                            <p class="new-arrivals" id="bestSeller2">Best Seller</p>
                        </div>
                    </div>
                    <div class="container">
                        <div class="container2">
                            <div class="cp-container">
                                <div class="left-cp-container">
                                    <span name="cp-model-title" class="cp-model-title">vivo v29</span>
                                    <div class="specifications-container">
                                        <ul name="specifications">
                                            <li>Qualcomm Snapdragon 778G Processor</li>
                                            <li>8GB RAM</li>
                                            <li>128GB Storage</li>
                                            <li>4500mAh Battery</li>
                                            <li>6.5" AMOLED Display</li>
                                        </ul><br>
                                    </div>
                                </div>
                                <div class="right-left-container">
                                    <span name="price" class="price">Php 1000</span><br>
                                    <img src="product-img/cp-vivo1.svg">
                                </div>
                            </div>
                            <a href="#">See full specs</a><br><br>
                            <button class="pre-order-btn">Pre Order Now</button>
                        </div>
                    </div>
                </div>
                <!---->
            </div>
        </form>

        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            let newArrivalsContainer = document.getElementById('newArrivalsContainer');
            let bestSellerContainer = document.getElementById('bestSellerContainer');
            let newArrivals = document.getElementById('newArrivals');
            let bestSeller = document.getElementById('bestSeller');
            let newArrivals2 = document.getElementById('newArrivals2');
            let bestSeller2 = document.getElementById('bestSeller2');

            bestSeller.addEventListener('click', function(event) {
                event.preventDefault();
                newArrivalsContainer.style.display = 'none';
                bestSellerContainer.style.display = 'block';
            });

            newArrivals2.addEventListener('click', function(event) {
                event.preventDefault();
                bestSellerContainer.style.display = 'none';
                newArrivalsContainer.style.display = 'block';
            });
        });
        </script>
    </body>
    </html>
