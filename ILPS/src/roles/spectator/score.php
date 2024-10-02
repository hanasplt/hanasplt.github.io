<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- font --> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    
    <!-- css --> 
    <link rel="stylesheet" href="../../../public/assets/css/style.css">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../../../public/assets/icons/logo.png" alt="Logo">
        </div>
        <nav>
            <a href="../../index.html" class="navbar">Home</a>
            <a href="#" class="navbar">Schedule</a>
            <a class="navbarbar" href="spectator.php">Dashboard</a>
            <a href="../../../public/login.php" class="login-btn">LOGIN</a>
        </nav>
    </header>
    <a href="spectator.php" class="back-link"><img src="../../../public/assets/icons/back.png"> Back</a>
    <div class="container-5">
        <div class="container-top">
            <p>Event: </p>
            <select id="event" class="combo-box" onchange="updateTable()">
                <option value="" selected disabled>Select an event</option>
            <?php
                    $conn = require_once '../../../config/db.php'; // Include Database Connection
                    
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "CALL sp_getEvents";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                <option value="<?php echo $row['eventID'] ?>"><?php echo $row['eventName'] ?></option>
                            <?php
                        }
                    }
                    $result->free();
                    $stmt->close();
                ?>
            </select>
        </div>
        <div class="container-center">
            <table class="styled-table-score" id="scoreTable">
                <!-- Table rows will be inserted here by JavaScript -->
            </table>
        </div>
    </div>
    <footer>
        <div class="footer-left">
            <h6>Intramural Leaderboard and Points System</h6>
            <p>Transform intramurals with our Leaderboard & Points System. Real-time updates, competitive environment, community engagement. Streamline organization, identify talent effortlessly. Elevate your intramural experience today!</p>
        </div>
        <div class="footer-right">
            <h6>CONTACT US</h6>
            <p class="footer-email"><img src="../public/assets/icons/contact-email.png" alt="Email">ilps.usep@gmail.com</p>
            <p class="footer-contact"><img src="../public/assets/icons/contact-num.png" alt="Phone">+63 994 155 8637</p>
        </div>
    </footer>
    <div class="footer-footer">
        <hr style="height:1px; border-width:0; color: #a7be54; background-color:#a7be54">
        <p>© 2024 Intramural Leaderboard and Points System. All Rights Reserved.</p>
    </div>
    <script src="../spectator/js/score.js"></script>
</body>
</html>
