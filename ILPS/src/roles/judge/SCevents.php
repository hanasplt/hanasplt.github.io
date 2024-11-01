<?php

require_once '../../../config/sessionConfig.php';
require_once '../../../config/db.php'; // Include Database Connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not
require_once 'judgePermissions.php'; // Retrieves judge permissions

$id = $_SESSION['userId'];
$judge_name = $row['firstName'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS | Record Score</title>
    <link rel="stylesheet" href="../judge/css/SCevents.css">

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

    <?php // Display criteria for judging - permitted to view
    if (in_array('judge_form_read', $judge_rights)) { ?>
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
    if (isset($_GET['event']) && isset($_GET['name'])) {
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