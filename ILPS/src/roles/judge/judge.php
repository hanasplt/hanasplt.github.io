<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['userId'] = $id;
}

require_once 'judgePermissions.php'; // Retrieves judge permissions

$sql = "CALL sp_getAnAcc(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$retval = $stmt->get_result();

if ($retval->num_rows > 0) {
    $row = $retval->fetch_assoc();
    $status = $row['log_status'];
    $judge_name = $row['firstName'];

    $retval->free();
    $stmt->close();
}

if ($status != 'finish') {
    header('Location: ../../../auth/mandatory/change_pass.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS | Judge</title>
    <link rel="stylesheet" href="../judge/css/judge.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                        <p class="user-name"><?php echo $judge_name; ?></p>
                        <p class="judge-name">JUDGE</p>
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
        <?php // Display Events - permitted to view
        if (in_array('judge_event_read', $judge_rights)) { ?>
            <div class="eventsDropdown">
                <h4>Events </h4>
                <?php
                $sql = "CALL sp_getAJudge(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                    while ($row = $retval->fetch_assoc()) {
                ?>
                        <a href="SCevents.php?event=<?php echo $row['eventId']; ?>&name=<?php echo $row['eventName']; ?>">
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

            <script src="../judge/js/judge.js"></script>
        <?php } else { // Display message - not permitted to view
            echo '
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Oops!</strong> You lack the permission to view the Judging Events.
                </div>
            ';
        } ?>
    </div>
</body>

</html>