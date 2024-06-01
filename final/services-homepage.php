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
   <div class="main-container1">
    <div class="bottom-container">
     <div class="bottom-top-container">
      <p class="cp-title">Services</p>
    </div>
    <div class="container">
      <div class="container2">
        <div class="cp-container">
          <div class="left-cp-container">
            <span class="cp-model-title">Phone Repair</span>
            <div class="specifications-container">
              <ul>
                <li>Screen replacement</li>
                <li>Battery repair</li>
                <li>Security Issue</li>
                <li>Charging port repair</li>
                <li>Reball processor</li>
                <li>Storage upgrade</li>
              </ul><br>
            </div>
          </div>
          <div class="right-left-container">
          <img src="icons/service1.svg">
          </div>
        </div>
      </div>
      <div class="container2">
        <div class="cp-container">
          <div class="left-cp-container">
            <span class="cp-model-title">Laptop Repair</span>
            <div class="specifications-container">
              <ul>
              <li>Screen replacement</li>
              <li>Battery repair</li>
              <li>IC repair</li>
              <li>Clean install</li>
              <li>RAM upgrade</li>
              <li>Storage upgrade</li>
              </ul><br>
            </div>
          </div>
          <div class="right-left-container">
            <img src="icons/service2.svg">
          </div>
        </div>
      </div>
      <div class="container2">
        <div class="cp-container">
         <div class="left-cp-container">
          <span class="cp-model-title">Customer Care</span>
          <div class="specifications-container">
           <ul>
             <li>Response 9 A.M. to 7 P.M. (Monday - Sunday)</li>
             <li>Free Device Check Up</li>
             <li>We answer every question asked</li>
           </ul><br>
          </div>
         </div>
         <div class="right-left-container">
          <img src="icons/service3.svg">
         </div>
        </div>
       </div>
      <div class="container2">
        <div class="cp-container">
         <div class="left-cp-container">
          <span class="cp-model-title">Book now for device repairs at Ches Shop.</span>
         </div>
         <div class="right-left-container">
          <img src="icons/service4.svg">
         </div>
        </div>
        <button class="pre-order-btn" id="bookBtn">Book Now</button>
      </div>
     </div>
    </div>

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

  let bookBtn = document.getElementById('bookBtn');

  bookBtn.onclick = () => {
        window.location.href = "appointment-form.php";
  }
  </script>
 </body>
</html>