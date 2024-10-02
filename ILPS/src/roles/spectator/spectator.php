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
    <title>Dashboard</title>
    <!-- font --> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- css --> 
    <link rel="stylesheet" href="../../../public/assets/css/style.css">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
</head>
<body onload="updateTable()">
    <header>
        <div class="logo">
            <img src="../../../public/assets/icons/logo.png" alt="Logo">
        </div>
        <nav>
            <a href="../../index.html" class="navbar">Home</a>
            <a href="spectator-sched.php" class="navbar">Schedule</a>
            <a class="navbarbar" href="spectator.php">Dashboard</a>
            <a href="../../../public/login.php" class="login-btn">LOGIN</a>
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
    <div class="container-4">
        <div class="container-left">
            <div class="curr-rank">
                <div class="rank-list">
                    <p>Current Ranking</p>
                    <table class="styled-table-rank" id="scoreTable">
                        <!-- Table rows will be inserted here by JavaScript -->
                    </table>
                </div>
            </div>
        </div>
        <div class="container-right">
            <div class="view-button">
                <a href="score.php" class="view-btn">View Scores for Each Event</a>
            </div>
            <div class="list-team">
                <div class="list-teams">
                    <p>TEAMS</p>
                    <table class="styled-table">
                        <?php
                            $sql = "CALL sp_getAllTeam";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                        <tr>
                            <td class="team-name"><?php echo $row['teamName'] ?></td>
                        </tr>
                                    <?php
                                }
                            }
                            $result->free();
                            $stmt->close();
                        ?>
                    </table>
                </div>
            </div>
        </div>
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
