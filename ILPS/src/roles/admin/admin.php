<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];
    $_SESSION['userId'] = $user_id;
}

require_once 'adminPermissions.php'; // Retrieves admin permissions

try {
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
} catch (Exception $e) {
    throw new Exception("Exception Error: " . $e->getMessage());
}

// get the total accounts
$search_query = "%";
$sql_count = "CALL sp_getAccountCount(?,?)";
$stmt = $conn->prepare($sql_count);
$stmt->bind_param("si", $search_query, $iddd);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_accounts = $row['total'];

$result->free();
$stmt->close();

// get the total number of teams
$sql_count_teams = "SELECT fn_getTeamCount() as total;";
$stmt = $conn->prepare($sql_count_teams);
$stmt->execute();
$result_teams = $stmt->get_result();


$row = $result_teams->fetch_assoc();
$total_teams = $row['total'];

$result_teams->free();
$stmt->close();

// get the total number of events
$sql_count_events = "CALL sp_getEvents();";
$stmt = $conn->prepare($sql_count_events);
$stmt->execute();
$result_events = $stmt->get_result();

$total_events = $result_events->num_rows;

$result_events->free();
$stmt->close();

// display 3 accounts
$sql = "CALL sp_getAcc(?,?);"; // display only 3 accounts for display
$limit = 3;
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $iddd, $limit);
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
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

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
            <p onclick="window.location.href = 'admin.php';" class="navbarbie" title="Home">Home</p>
            <div class="acc-hover">
                <div class="acc-btn-container">
                    <p onclick="window.location.href = 'accounts.php';" class="navbar" ; title="Accounts">Accounts</p>
                </div>
                <div class="account-dropdown">
                    <p onclick="window.location.href = 'roles.php';" class="dc-text">Role</p>
                </div>
            </div>
            <p onclick="window.location.href = 'teams.php';" class="navbar" title="Teams">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="navbar" title="Events">Events</p>
            <p onclick="window.location.href = 'schedule.php';" class="navbar" title="Schedule">Schedule</p>
        </nav>
        <nav class="nav-link-1">
            <div class="dropdown">
                <button class="dropbtn">
                    <img class="icon-img" src="../../../public/assets/icons/icon-user.jpg">
                    <div>
                        <p class="user-name"><?php echo $admin_name; ?></p>
                        <p class="administrator">ADMINISTRATOR</p>
                    </div>
                </button>
                <div class="dropdown-content">
                    <p onclick="window.location.href = 'view-profile.php';" class="dc-text" title="Profile">View Profile</p>
                    <p onclick="window.location.href = 'reports.php';" class="dc-text" title="Reports">Reports</p>
                    <p onclick="window.location.href = 'accesslog.php';" class="dc-text" title="Logs">Logs</p>
                    <p onclick="showConfirmationMsg()" class="dc-text" title="Backup">Backup and Reset</p>
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="dashboard">
        <p class="welcome">Welcome, <a> <?php echo $admin_name; ?></a>👋</p>
        <div class="dash-number">
            <?php if (in_array('user_read', $admin_rights) || in_array('team_read', $admin_rights) || in_array('event_read', $admin_rights)) { ?>
                <div class="number-dash">
                    <?php if (in_array('user_read', $admin_rights)) { ?>
                        <div class="num-accounts">
                            <img class="dash-img" src="../../../public/assets/icons/num-of-accs.jpg">
                            <div class="number-deets">
                                <p class="total-accounts">TOTAL ACCOUNTS</p>
                                <p class="total-number"><?php echo $total_accounts; ?></p>
                            </div>
                            <div class="view-btn">
                                <button onclick="window.location.href = 'accounts.php';" class="view-button">VIEW</button>
                            </div>
                        </div>
                    <?php }
                    if (in_array('team_read', $admin_rights)) { ?>
                        <div class="num-teams">
                            <img class="dash-img" src="../../../public/assets/icons/num-of-teams.jpg">
                            <div class="number-deets">
                                <p class="total-teams">TOTAL TEAMS</p>
                                <p class="total-number"><?php echo $total_teams; ?></p>
                            </div>
                            <div class="view-btn">
                                <button onclick="window.location.href = 'teams.php';" class="view-button">VIEW</button>
                            </div>
                        </div>
                    <?php }
                    if (in_array('event_read', $admin_rights)) { ?>
                        <div class="num-events">
                            <img class="dash-img" src="../../../public/assets/icons/num-of-events.jpg">
                            <div class="number-deets">
                                <p class="total-events">TOTAL EVENTS</p>
                                <p class="total-number"><?php echo $total_events; ?></p>
                            </div>
                            <div class="view-btn">
                                <button onclick="window.location.href = 'EventTeam.php';" class="view-button">VIEW</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="dash-banner">
                <div class="banner">
                    <div class="banner-left">
                        <h1>Compete, Unite, and Celebrate; Where Achievements Are Recognized!</h1>
                        <p>The Official Intramural Leaderboard and Points System of the University of Southeastern Philippines Tagum-Mabini Campus. This provides a transparent platform for tracking individual and team achievements, fostering a sense of community and camaraderie among participants.</p>
                    </div>
                    <div class="banner-right">
                        <img src="../../../public/assets/icons/dash-banner.png">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="intra-list">
        <?php if (in_array('user_read', $admin_rights)) { ?>
            <div class="accounts">
                <div class="accounts-title">
                    <p id="accs">Accounts</p>
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
                                    <p id="acc-type"><?php echo $row['type']; ?></p>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
                <p id="vwall" onclick="window.location.href = 'accounts.php';">View All</p>
            </div>
        <?php }

        $result->free();
        $stmt->close();

        if (in_array('team_read', $admin_rights)) { ?>
            <div class="teams">
                <div class="teams-title">
                    <p id="team">Teams</p>
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
                <p id="vwall" onclick="window.location.href = 'teams.php';">View All</p>
            </div>
        <?php }
        if (in_array('event_read', $admin_rights)) { ?>
            <div class="events">
                <div class="events-title">
                    <p id="event">Events</p>
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
                <p id="vwall" onclick="window.location.href = 'EventTeam.php';">View All</p>
            </div>
        <?php } ?>
    </div>
    <script src="../admin/js/admin.js"></script>
</body>

</html>