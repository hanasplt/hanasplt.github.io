<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
$conn = require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = isset($_GET['id']);
    $_SESSION['userId'] = $user_id;
}

$getAdmin = "CALL sp_getAnAcc(?)";

$iddd = $_SESSION['userId'];
$stmt = $conn->prepare($getAdmin);
$stmt->bind_param("i", $iddd);
$stmt->execute();
$retname = $stmt->get_result();

// Retrieve Admin Name
$row = $retname->fetch_assoc();
$admin_name = $row['firstName'];

$retname->free();
$stmt->close();

$sql = "CALL sp_getAcc(?);"; // display only 3 accounts for display
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo.svg">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
            <p onclick="window.location.href = 'admin.php';" class="navbarbie">Home</p>
            <p onclick="window.location.href = 'accounts.php';" class="navbar" ;>Accounts</p>
            <p onclick="window.location.href = 'teams.php';" class="navbar">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="navbar">Events</p>
            <p onclick="window.location.href = 'schedule.php';" class="navbar">Schedule</p>
        </nav>
        <nav class="nav-link">
            <div class="dropdown">
                <button class="dropbtn">
                    <img class="icon-img" src="../../../public/assets/icons/icon.png">
                    <p><?php echo $admin_name; ?></p>
                </button>
                <div class="dropdown-content">
                    <p onclick="window.location.href = '';" class="navbar">View Profile</p>
                    <p onclick="window.location.href = 'reports.php';" class="navbar">Reports</p>
                    <p onclick="window.location.href = '../admin/logs/accesslog.html';" class="navbar">Logs</p>
                    <div class="menu-icon">
                        <p id="logoutIcon">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="accounts">
        <div class="accounts-title">
            <p id="accs">Accounts</p>
            <p id="vwall" onclick="window.location.href = 'accounts.php';">View All</p>
        </div>
        <?php
        if ($result->num_rows > 0) { // fetch and display the results from database
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
        try {
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
                                <p id="name"><?php echo $row['teamName']; ?></p>
                            </div>
                        </div>
                    </div>
        <?php
                }
            }
            $result->free();
            $stmt->close();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
        ?>
    </div>

    <div class="events">
        <div class="events-title">
            <p id="event">Events</p>
            <p id="vwall" onclick="window.location.href = 'EventTeam.php';">View All</p>
        </div>

        <?php
        try {
            $sql = "CALL sp_getEventLimit(?)"; // retrieve 3 events for display
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
        ?>
                    <div class="ebent">
                        <div class="left-deets">
                            <div class="ebent-img">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>

                            <div class="event-deets">
                                <p id="name"><?php echo $row['eventName']; ?></p>
                            </div>
                        </div>
                    </div>
        <?php
                }
            }
            $result->free();
            $stmt->close();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
        ?>
    </div>

    <script src="../admin/js/admin.js"></script>

</body>

</html>