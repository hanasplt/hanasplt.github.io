<?php

$success = false;
if (isset($_POST['logout'])) {
    session_destroy();
    $success = true;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>CHES Cellphone and Accessories Shop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-solid-straight/css/uicons-solid-straight.css'>

    <!-- Web logo -->
    <link rel="icon" href="icons/logo.svg">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
      * {
          margin: 0;
          padding: 0;
      }

      body {
          font-family: "Inter", sans-serif;
          overflow-x: hidden;
          background-color: transparent;
          padding:0;
      }

      /* Header */
      .header-container {
          display: grid;
          grid-template-columns: 1fr 4fr;
          transition: 0.3s;
          width: 100%;
          z-index: 100;
          height: fit-content;
          position: fixed;
          margin-bottom: 10%;
      }

      .left-header {
          display: flex;
          background-color: #2A5181;
          align-items: center;
          column-gap: 15px;
          font-size: 17px;
          color: white;
          padding: 20px;
          padding-left: 50px;
      }

      .left-header p {
        font-family: "Krona One", sans-serif;
      }

      .navbar {
          background-color: #000F22;
          display: flex;
          justify-content: right;
          align-items: center;
          position: relative;
      }

      .menu {
          display: flex;
          justify-content: center;
          align-items: center;
          column-gap: 40px;
          padding-right: 50px;
          white-space: nowrap;
      }

      button {
          border: none;
      }

      .menu-btn {
          background-color: #000F22;
          color: white;
          font-size: 14px;
          padding: 20px;
          cursor: pointer;
      }

      .menu-btn:hover {
          background-color: #052355;
          padding: 20px;
          border-radius: 10px;
          transition: all 0.3s ease;
      }

      .menu-btn.active {
          background-color: #2A5181;
          color: white;
          font-size: 14px;
          padding: 20px;
          cursor: pointer;
          border-radius: 10px;
      }

      .right-header {
          padding-right: 60px;
          display: flex;
          column-gap: 40px;
          align-items: center;
      }

      .right-header a {
          text-decoration: none;
      }

      #account-btn {
          background-color: #000F22;
          padding: 10px 30px 10px 30px;
          cursor: pointer;
      }

      #account-btn:hover {
          background-color: #052355;
          border-radius: 10px;
          transition: all 0.3s ease;
      }

      .account-container {
          position: absolute;
          top: 14%;
          left: 75%;
          border-radius: 5px;
          width: 280px;
          z-index: 100;
          max-height: 0px;
          overflow: hidden;
          transition: max-height 0.5s;
          position: fixed;
      }

      .account-container.open {
          max-height: 200px;
      }

      .accounts-menu {
          color: white;
          background-color: #5381B2;
          padding-bottom: 20px;
          font-size: 19px;
          line-height: 50px;
      }

      .accounts-menu i {
          padding: 0 20px 0 20px;
      }

      .acc-menu-btn a {
          white-space: nowrap;
          text-decoration: none;
          color: white;
      }

      .acc-menu-btn:hover {
          background-color: #052355;
          transition: all 0.5s ease;
      }

      .btn {
          background-color: transparent;
          font-size: 19px;
          color: white;
      }

      #menu-icon {
          color: white;
          font-size: 20px;
          margin-top: 5px;
          cursor: pointer;
          z-index: 10001;
          display: none;
      }

      .x {
          color: white;
          font-size: 20px;
          margin-top: 5px;
          cursor: pointer;
          z-index: 10001;
          display: none;
      }

      /* Media Queries */
      @media only screen and (max-width: 1150px) {
          .account-container {
              left: 70%;
          }
      }

      @media only screen and (max-width: 1050px) {
          .account-container {
              left: 67%;
          }
      }

      @media only screen and (max-width: 1000px) {
          #menu-icon {
              display: block;
          }

          .left-header {
              font-size: 15px;
              padding-left: 30px;
          }

          .menu {
              position: absolute;
              top: 100%;
              right: -100%;
              width: 200px;
              height: 100vh;
              background-color: #000F22;
              display: flex;
              flex-direction: column;
              justify-content: flex-start;
              transition: all 0.5s ease;
          }

          .menu a {
              text-decoration: none;
          }

          .menu-btn {
              display: block;
              margin-left: 50px;
              margin-bottom: 20px;
          }

          .menu-btn:hover {
              width: 200px;
              border-radius: 0;
              background-color: #000F22;
          }

          .menu-btn-vivo {
              display: block;
              margin-left: 50px;
              margin-bottom: 20px;
              width: 180px;
              border-radius: 0;
          }

          .menu.open {
              right: 0;
              transition: all 0.5s ease;
          }

          .account-container {
              left: 59%;
          }
      }

      @media only screen and (max-width: 950px) {
          .account-container {
              left: 57%;
          }
      }

      @media only screen and (max-width: 850px) {
          .account-container {
              left: 52%;
          }
      }

      @media only screen and (max-width: 750px) {
          .account-container {
              left: 45%;
          }
      }

      @media only screen and (max-width: 650px) {
          .account-container {
              left: 37%;
          }
      }

      @media only screen and (max-width: 600px) {
          .account-container {
              left: 36%;
              width: 250px;
          }
      }

      @media only screen and (max-width: 550px) {
          .account-container {
              left: 30%;
          }

          .left-header {
              background-color: #000F22;
          }
      }

      @media only screen and (max-width: 500px) {
          .account-container {
              left: 34%;
              width: 200px;
          }

          .accounts-menu {
              font-size: 15px;
          }
      }

      @media only screen and (max-width: 460px) {
          .left-header {
              padding-left: 10px;
          }
      }

      @media only screen and (max-width: 400px) {
          .right-header {
              column-gap:20px;
              padding-right: 30px;
          }
      }

      @media only screen and (max-width: 360px) {
          .menu {
              width: 150px;
          }
      }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-container">
        <!-- Left Header -->
        <div class="left-header">
            <!-- Logo and Company Name -->
            <img src="icons/logo.svg">
            <p>CHES</p>
        </div>
        <!-- Navbar -->
        <div class="navbar">
            <!-- Menu -->
            <div class="menu" id="hidden-menu">
                <!-- Menu items -->
                <a href="cp-homepage.php"><button class="menu-btn">Cellphones</button></a>
                <a href="accessories-homepage.php"><button class="menu-btn">Accessories</button></a>
                <a href="services-homepage.php"><button class="menu-btn">Services</button></a>
                <a href="about-us.php"><button class="menu-btn">About Us</button></a>
            </div>
            <!-- Right Header -->
            <div class="right-header">
                <!-- Account Button -->
                <div id="account-btn">
                    <img src="icons/account.svg">
                    <!-- Account Menu (hidden by default) -->
                    <div class="account-container">
                        <!-- Account menu items -->
                        <div class="accounts-menu">
                            <form method="post" id="logoutForm">
                                <div class="acc-menu-btn" id="myProfile"><i class="fi fi-rr-user"></i>My Profile</div>
                                <div class="acc-menu-btn"><i class="fi fi-rr-reservation-table"></i><a href="my-reservations.html">My Reservations</a></div>
                                <div class="acc-menu-btn"><i class="fi fi-rr-reservation-table"></i><a href="my-appointments.html">My Appointments</a></div>
                                <div class="acc-menu-btn" id="logOut"><button type="submit" class="btn" name="logout"><i class="fi fi-rr-exit"></i>Log out</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Menu Icon (for mobile) -->
                <div id="menu-icon">
                    <i class="fi fi-br-bars-staggered"></i>
                </div>
                <!-- Close Icon (for mobile) -->
                <div class="x">
                    <i class="fi fi-br-cross"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Your JavaScript code here -->
    <script>
      // Handling account and menu interactions
      let hamburger = document.querySelector('#menu-icon');
      let menu = document.querySelector('.menu');
      let account = document.querySelector('#account-btn');
      let accountContainer = document.querySelector('.account-container');
      let x = document.querySelector('.x');

      hamburger.onclick = () => {
          if (!accountContainer.classList.contains('open')) {
              hamburger.style.display = 'none';
              x.style.display = 'block';
              menu.classList.toggle('open');
          } else {
              accountContainer.classList.remove('open');
              hamburger.style.display = 'block';
              x.style.display = 'none';
              menu.classList.toggle('open');
          }
      }

      x.onclick = () => {
          hamburger.style.display = 'block';
          x.style.display = 'none';
          menu.classList.remove('open');
      }

      account.onclick = () => {
          if (!menu.classList.contains('open')) {
              accountContainer.classList.toggle('open');
          } else {
              menu.classList.remove('open');
              accountContainer.classList.toggle('open');
          }
      }

      // Handling profile and logout actions
      let go = document.querySelector('#myProfile');

      go.onclick = () => {
          window.location.href = "my-profile.php";
      }

      const menuButtons = document.querySelectorAll('.menu-btn');
      const currentPath = window.location.pathname;

      // Set active class based on current page
      menuButtons.forEach(button => {
          const buttonHref = button.closest('a').getAttribute('href');
          if (buttonHref === currentPath) {
              button.classList.add('active');
          }

          button.addEventListener('click', function() {
              // Remove active class from all buttons
              menuButtons.forEach(btn => btn.classList.remove('active'));

              // Add active class to the clicked button
              this.classList.add('active');
          });
      });
    </script>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $success): ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Logging out..',
                text: 'Comeback anytime!',
                imageUrl: 'icons/logo1.png',
                imageAlt: 'Custom image', 
                confirmButtonColor: '#3085d6',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'sign-in.php';
            });
        });
    </script>
<?php endif; ?>

</body>
</html>
