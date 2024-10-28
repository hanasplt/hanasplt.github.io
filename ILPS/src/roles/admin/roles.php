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

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
            <p onclick="window.location.href = 'admin.php';" class="navbar" title="Home">Home</p>
            <div class="acc-hover">
                <div class="acc-btn-container">
                    <p onclick="window.location.href = 'accounts.php';" class="navbarbie" ; title="Accounts">Accounts</p>
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
                    <p onclick="window.location.href = '';" class="dc-text" title="Profile">View Profile</p>
                    <p onclick="window.location.href = 'reports.php';" class="dc-text" title="Reports">Reports</p>
                    <p onclick="window.location.href = 'accesslog.php';" class="dc-text" title="Logs">Logs</p>
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
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
                                    if (in_array('role_update', $admin_rights)) { ?>
                                        <div class="col data-row action">
                                            <p class="rowData"><i class="fa-solid fa-pen-to-square fa-xl edit-icon" data-user-id="<?php echo $userid; ?>" id="openPopup" title="Click to update user's access rights."></i></p>
                                        </div>
                                    <?php } else { // Display message - not permitted to update
                                        echo '<div class="col data-row action">
                               <p style="color: darkgrey;">Feature denied.</p>
                           </div>';
                                    } ?>
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
                    echo "<script>console.log(" . $e->getMessage() . ")</script>";
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
    } ?>
</body>

</html>