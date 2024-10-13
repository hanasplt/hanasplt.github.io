<?php
    require_once '../../../config/sessionConfig.php'; // Session Cookie
    require_once '../../../config/db.php'; // Database connection
    require_once '../admin/verifyLoginSession.php'; // Logged in or not
    require_once 'judgePermissions.php'; // Retrieves judge permissions

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $_SESSION['userId'] = $id;
    }

    $sql = "CALL sp_getAnAcc(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        $row = $retval->fetch_assoc();
        $status = $row['log_status'];

        $retval->free();
        $stmt->close();
    }

    if($status != 'finish') {
        header('Location: ../../../auth/mandatory/change_pass.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="../judge/css/judge.css">
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>JUDGE</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.html';" hidden>Home</p>
            <p onclick="window.location.href = 'accounts.html';" hidden>Accounts</p>
            <p onclick="window.location.href = 'create-team.html';" hidden>Teams</p>
            <p onclick="window.location.href = 'EventTeam.html';" hidden>Events</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>

    <?php // Display Events - permitted to view
    if (in_array('judge_event_read', $judge_rights)) {?>
    <div class="eventsDropdown">
        <h4>Events </h4>
            <?php
                $sql = "CALL sp_getAJudge(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $retval = $stmt->get_result();
        
                if ($retval->num_rows > 0) {
                    while($row = $retval->fetch_assoc()) {
                        ?>
                        <div class="event-item">
                            <a href="SCevents.php?event=<?php echo $row['eventId']; ?>&name=<?php echo $row['eventName']; ?>">
                                <?php echo $row['eventName']; ?>
                            </a>
                        </div>
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
</body>

</html>
