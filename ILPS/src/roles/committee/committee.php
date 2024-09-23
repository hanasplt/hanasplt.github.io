<?php
    require_once '../../../config/sessionConfig.php'; // Session Cookie
    $conn = require_once '../../../config/db.php'; // Database connection
    require_once '../admin/verifyLoginSession.php'; // Logged in or not

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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
        header('Location: ../../../auth/mandatory/change_passComt.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ILPS</title>
    <link rel="stylesheet" href="../committee/css/faci.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>COMMITTEE</i></p>
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
                while($row = $retval->fetch_assoc()) {
                    ?>
                        <div class="event-item">
                            <a href="Sevents.php?event=<?php echo $row['eventId']; ?>&name=<?php echo $row['eventName']; ?>">
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

    <script src="../committee/js/committee.js"></script>

    <?php $conn->close(); ?>
</body>

</html>
