<?php
$conn = require_once '../../../config/db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>

    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="../../../public/assets/css/spec-sched.css">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">
</head>

<body onload="updateTable()">

    <header>
        <div class="logo">
            <img src="../../../public/assets/icons/logo.png" alt="Logo">
        </div>
        <nav class="navbar">
            <div class="navbar-container">
                <div class="hamburger" id="hamburger">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
                <div class="nav-links" id="nav-links">
                    <a href="../../index.html" class="navbar">Home</a>
                    <a class="navbarbar" href="spectator.php">Schedule</a>
                    <a href="spectator.php" class="navbar">Dashboard</a>
                    <a href="../../../public/login.php" class="login-btn">LOGIN</a>
                </div>
            </div>
        </nav>
    </header>
    <div class="container-3">
        <div class="banner">
            <div class="banner-left">
                <h1>Compete, Rise, Repeat: Where Intramural Excellence Meets Friendly Competition!</h1>
                <p>Transform intramurals with our Leaderboard & Points System. Real-time updates, competitive environment, community engagement. Streamline organization, identify talent effortlessly. Elevate your intramural experience today!</p>
            </div>
            <div class="banner-right">
                <img src="../../../public/assets/icons/banner-3.png">
            </div>
        </div>
    </div>
    <div class="schedule-list">
        <table>
            <thead>
                <tr>
                    <th colspan="3">Day 1 - January 13, 2018</th>
                </tr>
                <tr>
                    <th class="head">Time</th>
                    <th class="head">Activity</th>
                    <th class="head">Location</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>08:00 AM</td>
                    <td>Opening Ceremony</td>
                    <td>Open Grounds</td>
                </tr>
                <tr>
                    <td>09:00 AM</td>
                    <td>Basketball Match</td>
                    <td>PECC Gymnasium</td>
                </tr>
                <tr>
                    <td>10:30 AM</td>
                    <td>Volleyball Match</td>
                    <td>Volleyball Court</td>
                </tr>
            </tbody>
        </table>
    </div>
    <footer>
        <div class="footer-left">
            <h6>Intramural Leaderboard and Points System</h6>
            <p>Transform intramurals with our Leaderboard & Points System. Real-time updates, competitive environment, community engagement. Streamline organization, identify talent effortlessly. Elevate your intramural experience today!</p>
        </div>
        <div class="footer-right">
            <h6>CONTACT US</h6>
            <p class="footer-email"><img src="../../../public/assets/icons/contact-email.png" alt="Email">ilps.usep@gmail.com</p>
            <p class="footer-contact"><img src="../../../public/assets/icons/contact-num.png" alt="Phone">+63 994 155 8637</p>
        </div>
    </footer>
    <div class="footer-footer">
        <hr style="height:1px; border-width:0; color: #a7be54; background-color:#a7be54">
        <p>© 2024 Intramural Leaderboard and Points System. All Rights Reserved.</p>
    </div>
    <script src="../spectator/js/spectator.js"></script>
</body>

</html>

<script>
    const navLinks = document.querySelector('.nav-links');
    const hamburger = document.querySelector('.hamburger');

    function toggleNav() {
        navLinks.classList.toggle('active');
    }

    hamburger.addEventListener('click', toggleNav);

    document.addEventListener('click', function(event) {
        if (!navLinks.contains(event.target) && !hamburger.contains(event.target)) {
            navLinks.classList.remove('active');
        }
    });
</script>