<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['userId'] = $id;
}

require_once 'committeePermissions.php'; // Retrieves committee permissions

$sql = "CALL sp_getAnAcc(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$retval = $stmt->get_result();

if ($retval->num_rows > 0) {
    $row = $retval->fetch_assoc();
    $status = $row['log_status'];
    $comm_name = $row['firstName'];

    $retval->free();
    $stmt->close();
}

if ($status != 'finish') {
    header('Location: ../../../auth/mandatory/change_passComt.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ILPS | Committee</title>
    <link rel="stylesheet" href="../committee/css/faci.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
        </nav>
        <nav class="nav-link-1">
            <div class="dropdown">
                <button class="dropbtn">
                    <img class="icon-img" src="../../../public/assets/icons/icon-user.jpg">
                    <div>
                        <p class="user-name"><?php echo $comm_name; ?></p>
                        <p class="comm-name">COMMITTEE</p>
                    </div>
                </button>
                <div class="dropdown-content">
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <div class="dash-banner">
            <div class="banner">
                <div class="banner-left">
                    <h1 class="intrams-name">INDIGAY 2024</h1>
                    <p class="intrams-theme">Uniting Through Talent, Embracing Diversity, and Empowering Inclusivity Within The Community</p>
                </div>
                <div class="banner-right">
                    <img src="../../../public/assets/icons/banner-4.png">
                </div>
            </div>
        </div>
        <?php // Display events - permitted to view
        if (in_array('committee_event_read', $comt_rights)) { ?>
            <div class="eventsDropdown">
                <h4>Events</h4>
                <?php
                // Retrieve and display events that involve this committee
                $sql = $sql = "CALL sp_getAComt(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                    while ($row = $retval->fetch_assoc()) {
                ?>
                        <a href="Sevents.php?event=<?php echo $row['eventId']; ?>">
                            <span class="hidden"><?php echo $row['eventName']; ?></span>
                            <div class="event-item">
                                <div class="left-deets">
                                    <div class="event-img">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                    <div class="event-deets">
                                        <p id="name"><?php echo $row['eventName']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                <?php
                    }
                    $retval->free();
                    $stmt->close();
                } else {
                    echo "<div class='no-event'>No event/s.</div>";
                }
                ?>
            </div>

            <script src="../committee/js/committee.js"></script>

        <?php } else {
            echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Managed Events.
            </div>
        ';
        }
        $conn->close(); ?>
    </div>
</body>

</html>