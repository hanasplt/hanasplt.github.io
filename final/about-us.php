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
  <link href="about-us.css" type="text/css" rel="stylesheet" />
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

 </head>
 <body>
   <div class="shop-bg">
        <div class="main-aboutus">
            <div>
                <p class="text-aboutus">About Us</p>
                <p class="text-aboutus2">Vladimir Buragay, a tech enthusiast and the owner of Ches Cellphone and Accessories, decided to offer reliable repairs using his skills and experience. With years of experience in cellphone and laptop/computer repair, he decided to open a shop offering a wide range of cellphones, accessories, and repair services.<br><br><span class="text-after-br">You're welcome to visit Ches Cellphone and Accessories Shop located at Stall 104, 2nd Floor of Bansalan Public Mall in Bansalan, Davao del Sur. For any questions or assistance, feel free to contact us by phone or email. Your satisfaction is our top priority</span></p>
            </div>
            <img src="icons/team.svg" class="team" alt="team" > 
        </div>
   </div>
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
  <div class="account-container">
   <div class="triangle"><i class="fi fi-ss-pyramid"></i></div>
   <div class="accounts-menu">
    <div class="acc-menu-btn" id="myProfile"><i class="fi fi-rr-user"></i>My Profile</div>
    <div class="acc-menu-btn"><i class="fi fi-rr-reservation-table"></i><a href="my-reservations.html">My Reservations</a></div>
    <div class="acc-menu-btn"><i class="fi fi-rr-reservation-table"></i><a href="my-appointments.html">My Appointments</a></div>
    <div class="acc-menu-btn" id="logOut"><i class="fi fi-rr-exit"></i>Log out</div>
   </div>
  </div>
  <!--script-->
  <script type="text/javascript" src="about-us.js"></script>
 </body>
</html>

