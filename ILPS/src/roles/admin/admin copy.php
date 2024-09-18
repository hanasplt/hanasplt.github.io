<?php
    session_start();

    $user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : '';

    $conn = include '../../../config/db.php';
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "CALL sp_getAcc(?);"; // display 3 accounts for display
    $limit = 3;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Admin</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../admin/css/admin.css">

        <!--font-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <!--Web-logo-->
        <link rel="icon" href="../../../public/assets/icons/logo.svg">

        <!-- icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">


        <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>
        <div class="nav-bar">
            <img class="logo-img" src="../../../public/assets/icons/logoo.png">
            <div class="logo-bar">
                <p>Intramural Leaderboard</p>
                <p>and Points System</p>
                <p id="administrator"><i>ADMINISTRATOR</i></p>
            </div>

            <div class="links">
                <p onclick="window.location.href = 'admin.php';"><b>Home</b></p>
                <p onclick="window.location.href = 'accounts.php';">Accounts</p>
                <p onclick="window.location.href = 'teams.php';">Teams</p>
                <p onclick="window.location.href = 'EventTeam.php';">Events</p>
                <p onclick="window.location.href = 'schedule.php';">Schedule</p>
                <p onclick="window.location.href = 'reports.php';">Reports</p>
                <p onclick="window.location.href = 'accesslog.php';">Logs</p>
            </div>

            <div class="menu-icon">
                <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
            </div>
        </div>

        <div class="dash">
            <div class="create">
                <div class="new-account" id="openPopup">
                    <div class="plus-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    
                    <div class="new-account-info">
                        <p id="create">Create a New Account</p>
                        <p id="add">Add an account for committee/judge.</p>
                    </div>
                </div>

                    <div class="iframe-overlay" id="iframeOverlay">
                        <iframe id="popupFrame"></iframe>
                    </div>

                    <script>
                        document.getElementById("openPopup").addEventListener("click", function() {
                            document.getElementById("popupFrame").src = "create-account.html";
                            document.getElementById("iframeOverlay").style.display = "block";
                        });
    
                        window.addEventListener("message", function(event) {
                            if (event.data === "closePopup") {
                                document.getElementById("iframeOverlay").style.display = "none";
                            }
                        });
                    </script>

                <div class="new-team" onclick="location.href = 'teams.php'">
                    <div class="plus-icon">
                        <i class="fas fa-plus"></i>
                    </div>

                    <div class="new-team-info">
                        <p id="create">Create a New Team</p>
                        <p id="add">Add a new team.</p>
                    </div>

                </div>

                <div class="new-event" onclick="location.href = 'EventTeam.php'">
                    <div class="plus-icon">
                        <i class="fas fa-plus"></i>
                    </div>

                    <div class="new-event-info">
                        <p id="create">Create a New Event</p>
                        <p id="add">Add a new event.</p>
                    </div>
                </div>
            </div>

            <div class="info">
                <div class="info-text">
                    <p id="comp">Compete, Rise, Repeat:</p>
                    <p id="tagline">Where the Intramural Excellence
                        Meets Friendly Competition!
                    </p>
                </div>
                <img src="assets/icons/ILPS LOGO 2.png">
            </div>
        </div>

        <div class="accounts">
            <div class="accounts-title">
                <p id="accs">Accounts</p>
                <p id="vwall" onclick="window.location.href = 'accounts.php';">View All</p>
            </div>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
            <div class="account">
                <div class="left-deets">
                    <div class="acc-img">
                        <i class="fas fa-user"></i> 
                    </div>
    
                    <div class="acc-deets">
                        <p id="name"><?php echo $row['firstName']; ?></p>
                        <p><?php echo $row['type']; ?></p>
                    </div>
                </div>
            </div>
                            <?php
                        }
                    }
                    $result->free();
                    $stmt->close();
                ?>
        </div>

        <div class="teams">
            <div class="teams-title">
                <p id="team">Teams</p>
                <p id="vwall" onclick="window.location.href = 'teams.php';">View All</p>
            </div>

            <?php 
                $sql = "CALL sp_getTeam(?, ?)"; // retrieving 3 teams for display
                $opsit = 0;
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $limit, $opsit);
                $stmt->execute();
                $result = $stmt->get_result();   

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
            <div class="tim">
                <div class="left-deets">
                    <div class="tim-img">
                        <i class="fa-solid fa-people-group"></i>
                    </div>
    
                    <div class="tim-deets">
                        <p id="name"><?php echo $row['teamName'];?></p>
                    </div>
                </div>
            </div>
                        <?php
                    }
                }
                $result->free();
                $stmt->close();
            ?>
        </div>
        
        <div class="events">
            <div class="events-title">
                <p id="event">Events</p>
                <p id="vwall" onclick="window.location.href = 'EventTeam.php';">View All</p>
            </div>

            <?php 
                $sql = "CALL sp_getEventLimit(?)"; // retrieve 3 events for display
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();   

                if ($result->num_rows > 0)  {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="ebent">
                            <div class="left-deets">
                                <div class="ebent-img">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                
                                <div class="event-deets">
                                    <p id="name"><?php echo $row['eventName'];?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                $result->free();
                $stmt->close();
            ?>
        </div>

        <!-- logout confirmation -->
    <script>
        document.getElementById('logoutIcon').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7FD278',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log me out',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // mag redirect siya to the login page
                    window.location.href = 'index.html';
                }
            });
        });
    </script>

    </body>
</html>