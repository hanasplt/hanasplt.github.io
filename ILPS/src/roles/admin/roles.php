<?php
// log errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../../config/sessionConfig.php'; // Session
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Login Verification
require_once 'adminPermissions.php'; // Retrieves admin permissions

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles</title>

    <!-- CSS -->
     <link rel="stylesheet" href="css/bootstrap-grid.min.css">
     <link rel="stylesheet" href="css/roles.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo.svg">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>ADMINISTRATOR</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.php';">Home</p>
            <p onclick="window.location.href = 'accounts.php';"><b>Accounts</b></p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';">Events</p>
            <p onclick="window.location.href = 'schedule.php';">Schedule</p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
            <p onclick="window.location.href = '../admin/logs/accesslog.html';">Logs</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>
    <!-- End of Navbar -->
    
    <?php
        // Display users - permitted to view
        if (in_array('role_read', $admin_rights)) {
    ?>
    <!-- Roles and Permissions -->
     <div class="container">
        <!-- Popup iFrame for editing user's permissions -->
        <div class="iframe-overlay" id="iframeOverlay">
            <iframe id="popupFrame"></iframe>
        </div>
        <h2 class="roleTitle">Roles</h2>
        <p class="roleDescrip">Modify user's access rights</p>
        <div class="role-container">
            <!-- Row Header -->
             <div class="row">
                <div class="col-8 main-header">
                    <p class="rowTitle">Users</p>
                </div>
                <div class="col main-header action">
                    <p class="rowTitle">Action</p>
                </div>
             </div>
            
            <!-- Row Data -->
             <?php
             try {
                 // Retrieve users from the database
                 $getUsers = "CALL sp_getAllAcc";
    
                 $stmt = $conn->prepare($getUsers);
                 $stmt->execute();
                 $retval = $stmt->get_result();
    
                 if ($retval->num_rows > 0) {
                    // Display Users
                    while ($row = $retval->fetch_assoc()) {
                        // Loop user's full name
                        $userid = $row['userId'];
                        $fname = $row['firstName'];
                        $lname = $row['lastName'];
                        $fullname = $fname . " " . $lname;

                        if ($userid != 1) {
                            // Display all users except the main admin
                        ?>
                        <div class="row">
                           <div class="col-8 data-row user-name" onclick="showRoleDetails(<?php echo $userid; ?>)" title="Click to view user's role.">
                               <p class="rowData"><?php echo $fullname; ?></p>
                           </div>

                           <?php // Display edit-icon - permitted to update
                           if (in_array('role_update', $admin_rights)) {?>
                           <div class="col data-row action">
                               <p class="rowData"><i class="fa-solid fa-pen-to-square fa-xl edit-icon" data-user-id="<?php echo $userid; ?>" id="openPopup" title="Click to update user's access rights."></i></p>
                           </div>
                           <?php } else { // Display message - not permitted to update
                           echo '<div class="col data-row action">
                               <p style="color: darkgrey;">Feature denied.</p>
                           </div>';
                           }?>
                        </div>
                        <?php
                        }
                    }
                 } else {
                    // Display no user message
                    ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>FYI: </strong> No User/s exists.
                    </div>
                    <?php
                 }
             } catch (Exception $e) {
                echo "<script>console.log(".$e->getMessage().")</script>";
             }
             ?>
        </div>
     </div>
    <!-- End of Roles and Permissions -->

    <script src="js/role.js"></script>
    <?php } else { // Display message - not permitted to view
        echo '
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>FYI: </strong> You lack the permission to view the Role features.
            </div>
        ';
    }?>
</body>
</html>