<?php

require_once '../../../config/sessionConfig.php';
require_once '../../../config/db.php'; // Include Database Connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not
require_once 'judgePermissions.php'; // Retrieves judge permissions

$id = $_SESSION['userId'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="../judge/css/SCevents.css">
    <link rel="icon" href="../../../public/assets/icons/logo.svg">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png" alt="Logo">
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

    <?php // Display criteria for judging - permitted to view
    if (in_array('judge_form_read', $judge_rights)) {?>
    <!-- GENERAL CRITERIA -->
    <form method="post" enctype="multipart/form-data" id="criteriaForm">
    </form>
    <?php } else {
        echo '
        <div class="alert-container">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view the Criteria for Judging.
            </div>
        </div>
        ';
    } ?>

    <script src="../judge/js/SCevents.js"></script>

    <?php
    // When 'event' and 'name' is set, criteria will be loaded
        if(isset($_GET['event']) && isset($_GET['name'])) {
            $evId = $_GET['event'];
            $evname = $_GET['name'];

            if (in_array('judge_form_read', $judge_rights)) {
            echo "<script>loadEventCriteria($evId, '$evname')</script>";
            }
        }

        $conn->close();
    ?>
</body>

</html>
