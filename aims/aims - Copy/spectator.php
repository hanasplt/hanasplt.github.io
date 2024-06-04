<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- font --> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    
    <!-- css --> 
    <link rel="stylesheet" href="assets/css/style.css">

    <!--Web-logo-->
    <link rel="icon" href="/assets/icons/logo-1.png">
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/icons/logo.png" alt="Logo">
        </div>
        <nav>
            <a href="landing-page.html" class="navbar">Home</a>
            <a href="landing-page.html" class="navbar">About Us</a>
            <a class="navbarbar" href="spectator.php">Dashboard</a>
            <a href="login.php" class="login-btn">LOGIN</a>
        </nav>
    </header>
    <div class="container-3">
        <div class="banner">
            <div class="banner-left">
                <h1>Compete, Rise, Repeat: Where Intramural Excellence Meets Friendly Competition!</h1>
                <p>Transform intramurals with our Leaderboard & Points System. Real-time updates, competitive environment, community engagement. Streamline organization, identify talent effortlessly. Elevate your intramural experience today!</p>
            </div>
            <div class="banner-right">
                <img src="assets/icons/banner-3.png">
            </div>
        </div>
    </div>
    <div class="container-4">
        <div class="container-left">
            <div class="curr-rank">
                <div class="rank-list">
                    <p>Current Ranking</p>
                    <table class="styled-table-rank">
                        <tr>
                            <th class="rank-column">Rank</th>
                            <th class="name-column">Team Name</th>
                            <th class="points-column">Points</th>
                        </tr>
                            <?php
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $database = "ilpsystem";
                                
                                $conn = new mysqli($servername, $username, $password, $database);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $sql = "CALL sp_getLeaderboard";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $rank = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                    <tr>
                                        <td><?php echo $rank ?></td>
                                        <td><img src="assets/icons/sample.png"> <?php echo $row['teamName'] ?></td>
                                        <td><?php echo $row['points'] ?></td>
                                    </tr>
                                        <?php
                                        $rank++;
                                    }
                                }
                                $result->free();
                                $stmt->close();
                            ?>
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
                            <td><img src="assets/icons/sample.png"> <?php echo $row['teamName'] ?></td>
                        </tr>
                                    <?php
                                }
                            }
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
            <p class="footer-email"><img src="assets/icons/contact-email.png" alt="Email">john.doe@example.com</p>
            <p class="footer-contact"><img src="assets/icons/contact-num.png" alt="Phone">(555) 123-4567</p>
            <p class="footer-add">123 Street Barangay Apokon, Tagum City, Davao Del Norte</p>
        </div>
    </footer>
    <div class="footer-footer">
        <hr style="height:1px; border-width:0; color: #60A85A; background-color:#60A85A">
        <p>© 2024 Dreamy Inc. All Rights Reserved.</p>
    </div>
</body>
</html>
