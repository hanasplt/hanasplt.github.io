<?php
$conn = require_once '../../../config/db.php';

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
        <?php
            $query_teams = "CALL sp_getAllTeam()";
            $result_teams = $conn->query($query_teams);
            
            $teams = [];
            if ($result_teams->num_rows > 0) {
                while ($row_team = $result_teams->fetch_assoc()) {
                    $teams[$row_team['teamId']] = $row_team['teamName'];
                }
            }
            
            $result_teams->free();
            $conn->next_result();

            $query_days = "SELECT * FROM scheduled_days";
            $result_days = $conn->query($query_days);

            $scheduled_days = [];

            if ($result_days->num_rows > 0) {
                while ($row_day = $result_days->fetch_assoc()) {
                    $day_id = $row_day['id'];

                    $query_events = "SELECT * FROM scheduled_eventstoday WHERE day_id = ? ORDER BY time ASC";
                    $stmt_events = $conn->prepare($query_events);
                    $stmt_events->bind_param("i", $day_id);
                    $stmt_events->execute();
                    $result_events = $stmt_events->get_result();

                    $events = [];
                    if ($result_events->num_rows > 0) {
                        while ($row_event = $result_events->fetch_assoc()) {
                            $teamA_id = $row_event['teamA'];
                            $teamB_id = $row_event['teamB'];

                            $teamA_name = isset($teams[$teamA_id]) ? $teams[$teamA_id] : '';
                            $teamB_name = isset($teams[$teamB_id]) ? $teams[$teamB_id] : '';

                            $row_event['teamA_name'] = $teamA_name;
                            $row_event['teamB_name'] = $teamB_name;

                            $events[] = $row_event;
                        }
                    }

                    $row_day['events'] = $events;
                    $scheduled_days[] = $row_day;
                }
            }

            usort($scheduled_days, function ($a, $b) {
                return strtotime($a['day_date']) - strtotime($b['day_date']);
            });

            echo '<div class="schedule-table">';
            $dayCounter = 1;
            foreach ($scheduled_days as $day) {
                echo '<table>';
                echo '<thead>';
                echo '<tr><th colspan="8">Day ' . $dayCounter . ' - ' . date("F j, Y", strtotime($day['day_date'])) . '</th></tr>';
                echo '<tr>
                        <th class="head">Time</th>
                        <th class="head">Type</th>
                        <th class="head">Activity</th>
                        <th class="head">Game No.</th>
                        <th class="head">Team A</th>
                        <th class="head">Team B</th>
                        <th class="head">Location</th>
                        <th class="head">Status</th>
                    </tr>';
                echo '</thead>';
                echo '<tbody>';

                if (empty($day['events'])) {
                    echo '<tr><td colspan="8">No events scheduled for this day.</td></tr>';
                } else {
                    foreach ($day['events'] as $event) {
                        if ($event['status'] === 'Ongoing') {
                            echo '<tr style="font-weight: bold;">';
                        } else {
                            echo '<tr>';
                        }
                        
                        echo '<td>' . date("h:i A", strtotime($event['time'])) . '</td>';
                        echo '<td>' . htmlspecialchars($event['type']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['activity']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['gameNo']) . '</td>';

                        if (strtolower($event['type']) === 'sports') {
                            if (is_null($event['ResultA']) && is_null($event['ResultB'])) {
                                echo '<td>' . htmlspecialchars($event['teamA_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($event['teamB_name']) . '</td>';
                            } else {
                                $teamA_status = '';
                                $teamB_status = '';
                                $teamA_color = 'black';
                                $teamB_color = 'black';
                            
                                if ($event['ResultA'] === 'Winner') {
                                    $teamA_status = ' (Winner)';
                                    $teamA_color = 'green';
                                } elseif ($event['ResultA'] === 'Loser') {
                                    $teamA_status = ' (Loser)';
                                    $teamA_color = 'red';
                                }
                            
                                if ($event['ResultB'] === 'Winner') {
                                    $teamB_status = ' (Winner)';
                                    $teamB_color = 'green';
                                } elseif ($event['ResultB'] === 'Loser') {
                                    $teamB_status = ' (Loser)';
                                    $teamB_color = 'red';
                                }
                                
                                echo '<td>' . htmlspecialchars($event['teamA_name']) . '<span style="color: ' . $teamA_color . ';">' . $teamA_status . '</span></td>';
                                echo '<td>' . htmlspecialchars($event['teamB_name']) . '<span style="color: ' . $teamB_color . ';">'. $teamB_status . '</span></td>';                            
                            }
                                                     
                        } elseif (strtolower($event['type']) === 'socio-cultural') {
                            echo '<td colspan="2" style="text-align: center;">Check results at the <a href="spectator.php" style="color: blue; text-decoration: underline;">Dashboard</a> tab</td>';
                        } else {
                            echo '<td>' . htmlspecialchars($event['teamA_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($event['teamB_name']) . '</td>';
                        }
                
                        echo '<td>' . htmlspecialchars($event['location']) . '</td>';
                        echo '<td>' . htmlspecialchars($event['status']) . '</td>';
                        
                        echo '</tr>';
                    }
                }

                echo '</tbody>';
                echo '</table>';

                $dayCounter++; 
            }
            echo '</div>';

            $conn->close();
        ?>
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
