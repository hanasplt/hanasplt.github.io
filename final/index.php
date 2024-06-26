<!DOCTYPE html>
<html>
<head>
  <title>CHES Cellphone and Accessories Shop</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!--css-->
  <link href="landing-page.css" type="text/css" rel="stylesheet" />
  <!---->

  <!--fonts-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
  <!---->

  <!--icons-->
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
  <!---->

  <!--Web-logo-->
  <link rel="icon" href="icons/logo.svg">
  <!---->
</head>
<body>
  <!--header-->
  <div class="header-container">
   <div class="left-header">
    <img src="icons/logo.svg">
    <p>CHES</p>
   </div>
   <div class="navbar">
    <div class="menu" id="hidden-menu">
     <a href="sign-up.html"><button class="menu-btn">Cellphones</button></a>
     <a href="sign-up.html"><button class="menu-btn">Accessories</button></a>
     <a href="sign-up.html"><button class="menu-btn">Services</button></a>
     <a href="sign-up.html"><button class="menu-btn">About Us</button></a>
    </div>
    <div class="right-header">
     <a href="sign-in.php"><button class="sign-in-btn">SIGN IN</button></a>
     <div id="menu-icon">
      <i class="fi fi-br-bars-staggered"></i>
     </div>
     <div class="x">
      <i class="fi fi-br-cross"></i>
     </div>
    </div>
   </div>
  </div>
  <!---->

  <!--landing page main-->
  <div class="main-container">
   <img src="bg-img/ches-bg.png" class="bg-img" alt="CHES Shop">
   <div class="content">
    <p class="title">Welcome!</p>
    <p class="subtitle">Connect to Ches Cellphone and Accessories Shop, where we provide cellphone/accessories reservations and booking for gadget repairs. To avail these services, create an account.</p>
    <a href="sign-up.html"><button class="sign-up-btn">SIGN UP</button></a>
   </div>
  </div>
  <!---->

  <div class="space"></div>

  <!--cp new arrivals container-->
  <div class="cp-product-container">
   <div class="cp-container">
    <p class="cellphone-txt">Cellphones</p>
    <div class="phone-choices">
     <a href="#" class="cp-new-arrivals">New Arrivals</a>
     <a href="#" class="cp-best-seller">Best Seller</a>
    </div>
   </div>
   <div class="cellphones">
    <?php
    // Database connection details
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

    // Fetch products from the database
    $sql = "SELECT prod_brand, prod_model, prod_images, prod_unit_price 
        FROM inventory 
        WHERE prod_type = 'Phone' 
        ORDER BY prod_id DESC 
        LIMIT 3";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo '<div class="cellphone-container">';
            echo '<p class="cp-title">' . $row["prod_brand"] . ' ' . $row["prod_model"] . '</p><br>';
            echo '<img width="150" src="data:image/jpeg;base64,' . base64_encode($row["prod_images"]) . '" alt="' . $row["prod_brand"] . ' ' . $row["prod_model"] . '">';
            echo '<p style="font-size: 13px;">Php ' . $row["prod_unit_price"] . '</p><br><br>';
            echo '<a href="sign-up.html"><button class="pre-order-btn">Pre Order Now</button></a>';
            echo '</div>';
        }
    } else {
        echo "0 results";
    }
    ?>
   </div>
   <div class="view-more-container">
    <a href="sign-up.html"><button class="view-more-btn">View More</button></a>
   </div>
  </div>
  <!---->

  <!--cp best seller container//hidden-->
  <div class="cp-product-container2">
   <div class="cp-container2">
    <p class="cellphone-txt">Cellphones</p>
    <div class="phone-choices">
     <a href="#" class="cp-new-arrivals2">New Arrivals</a>
     <a href="#" class="cp-best-seller2">Best Seller</a>
    </div>
   </div>
   <div class="cellphones">
    <!-- Similar PHP code can be used here to fetch and display best sellers -->
   </div>
   <div class="view-more-container">
    <a href="sign-up.html"><button class="view-more-btn">View More</button></a>
   </div>
  </div>
  <!---->

  <!--accessories new arrivals container-->
  <div class="acc-product-container">
   <div class="acc-container">
    <p class="accessories-txt">Accessories</p>
    <div class="accessories-choices">
     <a href="#" class="acc-new-arrivals">New Arrivals</a>
     <a href="#" class="acc-best-seller">Best Seller</a>
    </div>
   </div>
   <div class="accessories">
    <?php
    // Fetch accessories from the database
    $sql = "SELECT prod_brand, prod_model, prod_images, prod_unit_price 
        FROM inventory 
        WHERE prod_type = 'Accessory' 
        ORDER BY prod_id DESC 
        LIMIT 3";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo '<div class="accessories-container">';
            echo '<p class="cp-title">' . $row["prod_brand"] . ' ' . $row["prod_model"] . '</p><br>';
            echo '<img width="150" src="data:image/jpeg;base64,' . base64_encode($row["prod_images"]) . '" alt="' . $row["prod_brand"] . ' ' . $row["prod_model"] . '">';
            echo '<p style="font-size: 13px;">Php ' . $row["prod_unit_price"] . '</p><br><br>';
            echo '<a href="sign-up.html"><button class="pre-order-btn">Pre Order Now</button></a>';
            echo '</div>';
        }
    } else {
        echo "0 results";
    }
    ?>
   </div>
   <div class="view-more-container">
    <a href="sign-up.html"><button class="view-more-btn">View More</button></a>
   </div>
  </div>
  <!---->

  <!--accessories best seller container//hidden-->
  <div class="acc-product-container2">
   <div class="acc-container2">
    <p class="accessories-txt">Accessories</p>
    <div class="accessories-choices">
     <a href="#" class="acc-new-arrivals2">New Arrivals</a>
     <a href="#" class="acc-best-seller2">Best Seller</a>
    </div>
   </div>
   <div class="accessories">
    <!-- Similar PHP code can be used here to fetch and display best sellers -->
   </div>
   <div class="view-more-container">
    <a href="sign-up.html"><button class="view-more-btn">View More</button></a>
   </div>
  </div>
  <!---->

  <!--our services container-->
  <div class="services-container">
    <p class="services-txt">Our Services</p>
   <div class="services-division">
    <div class="services">
     <div class="phone-repair-container">
      <p>Phone Repair</p><br>
      <ul>
       <li>LCD replacement</li>
       <li>Battery repair</li>
       <li>Security issue</li>
       <li>Charging port repair</li>
       <li>Reball processor</li>
      </ul>
      <a href="sign-up.html"><button class="book-now-btn">Book Now</button></a>
     </div>
     <div>
      <img src="product-img/phone-repair.svg">
     </div>
    </div>
    <div class="services2">
     <div class="laptop-repair-container">
      <p>Laptop Repair</p><br>
      <ul>
       <li>LCD replacement</li>
       <li>Battery repair</li>
       <li>Security issue</li>
       <li>IC repair</li>
       <li>Clean install</li>
      </ul>
      <a href="sign-up.html"><button class="book-now-btn">Book Now</button></a>
     </div>
     <div>
      <img src="product-img/laptop-repair.svg">
     </div>
    </div>
   </div>
  </div>
  <!---->

  <!--get in touch-->
  <div class="contacts-container">
   <p class="get-in-touch">GET IN TOUCH</p>
   <div class="contacts">
    <div class="location">
     <div><img src="icons/location.svg"></div>
     <p style="font-size: 16px;">Shop's Address</p><br>
     <a class="description">Stall 104 2nd Floor,<br> Bansalan Public Mall,<br> Bansalan, Davao del Sur</a>
    </div>
    <div class="phone-number">
     <div><img src="icons/phone-number.svg"></div>
     <p style="font-size: 16px;">Phone Number</p><br>
     <a class="description">(+63)955 906 5646</a>
    </div>
    <div class="email">
     <div><img src="icons/email.svg"></div>
     <p style="font-size: 16px;">Email Address</p><br>
     <a href="tonton.buragay@gmail.com">tonton.buragay@gmail.com</a>
    </div>
    <div class="facebook">
     <div><img src="icons/facebook.svg"></div>
     <p style="font-size: 16px;">Facebook</p><br>
     <a href="https://www.facebook.com/profile.php?id=100089788597426">Ches Cellphone and<br>Accessories Shop<br>(sales and repairing)</a>
    </div>
   </div>
  </div>
  <!---->
<!--script-->
<script type="text/javascript" src="landing-page.js"></script>
</body>
</html>
