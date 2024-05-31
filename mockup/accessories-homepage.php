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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
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
      <select class="dropdown">
       <option value="phone-case" selected>Phone Case</option> 
       <option value="charger">Charger</option> 
       <option value="keyboard">Keyboard</option> 
       <option value="mouse">Mouse</option> 
       <option value="tempered-glass">Tempered Glass</option> 
       <option value="headset">Headset</option> 
       <option value="headphone">Headphone</option> 
       <option value="powerbank">Powerbank</option> 
       <option value="watch">Watch</option> 
      </select>
     </div>
     <div class="search">
      <div class="search-container">
       <input type="text" name="search-text" class="search-input"><button class="search-btn" name="search"><b>Search</b><img class="search-icon" src="icons/search.svg"></button>
      </div>
     </div>
    </div>

    <div class="bottom-container" id="newArrivalsContainer">
     <div class="bottom-top-container">
      <p class="cp-title">Phone Case</p>
      <div class="bottom-top-right-container">
       <p class="new-arrivals" id="newArrivals">New Arrivals</p>
       <p class="best-seller" id="bestSeller">Best Seller</p>
      </div>
     </div>
     <div class="container">
      <div class="container2">
       <div class="cp-container">
        <div class="left-cp-container">
         <span name="acc-model-title" class="cp-model-title">Infinix Note 30 5G Shockproof Case</span>
         <div class="specifications-container">
          <ul name="details">
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
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
      <div class="container2">
       <div class="cp-container">
        <div class="left-cp-container">
         <span name="cp-model-title" class="cp-model-title">Infinix Note 30 5G Shockproof Case</span>
         <div class="specifications-container">
          <ul name="specifications">
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
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

    <!--hidden//best-seller-->
    <div class="bottom-container" id="bestSellerContainer">
     <div class="bottom-top-container">
      <p class="cp-title">Phone Case</p>
      <div class="bottom-top-right-container">
       <p class="best-seller" id="newArrivals2">New Arrivals</p>
       <p class="new-arrivals" id="bestSeller2">Best Seller</p>
      </div>
     </div>
     <div class="container">
      <div class="container2">
       <div class="cp-container">
        <div class="left-cp-container">
         <span name="cp-model-title" class="cp-model-title">Infinix Note 30 5G Shockproof Case</span>
         <div class="specifications-container">
          <ul name="specifications">
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
           <li>Qualcomm Snapdragon 778G Processor</li>
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