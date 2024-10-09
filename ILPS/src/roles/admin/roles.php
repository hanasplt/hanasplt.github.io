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
    <link rel="icon" href="../../../../public/assets/icons/logo.svg">

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
    <!-- Create New Role Popup -->
    <div class="new-account" id="openPopup">
        <div class="plus-icon">
            <i class="fas fa-plus"></i>
        </div>
        <div class="new-account-info">
            <p id="create">Create New Role</p>
            <p id="add">Add a role for the users.</p>
        </div>
        <div class="iframe-overlay" id="iframeOverlay">
            <iframe id="popupFrame"></iframe>
        </div>
    </div>
    <!-- End of Create New Role Popup -->
    <!-- Roles and Permissions -->
     <div class="container">
        <h2 class="roleTitle">Roles</h2>
        <div class="role-container">
            <!-- Row Header -->
            <div class="row">
                <div class="col-3 main-row">
                    <p class="roleName">Role</p>
                </div>
                <!-- Change this, built in -->
                <div class="col-1 main-row">
                    <p class="roleFuncName">Create Account</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Update Account</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Account</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Create Teams</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Update Teams</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Teams</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Create Events</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Update Events</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Events</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Add Contestants</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Contestants</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Add Committee</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Committee</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Add Judge</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Judge</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Create Criteria</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Update Criteria</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Criteria</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Add Scoring</p>
                </div>
                <div class="col-1 main-row">
                    <p class="roleFuncName">Delete Scoring</p>
                </div>
                <div class="col-2 main-row">
                    <p class="roleFuncName">Create Scheduled Day</p>
                </div>
                <div class="col-2 main-row">
                    <p class="roleFuncName">Update Scheduled Day</p>
                </div>
                <div class="col-2 main-row">
                    <p class="roleFuncName">Delete Scheduled Day</p>
                </div>
                <div class="col-2 main-row">
                    <p class="roleFuncName">Create Scheduled Event</p>
                </div>
                <div class="col-2 main-row">
                    <p class="roleFuncName">Update Scheduled Event</p>
                </div>
                <div class="col-2 main-row">
                    <p class="roleFuncName">Delete Scheduled Event</p>
                </div>
            </div>
            <!-- Data Row -->
            <div class="row">
                <div class="col-3">
                    <p class="roleName">RoleName</p>
                </div>
                <!-- Change this, built in -->
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-1">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-2">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-2">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-2">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-2">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-2">
                    <p class="roleFuncName">Yes</p>
                </div>
                <div class="col-2">
                    <p class="roleFuncName">Yes</p>
                </div>
            </div>
        </div>
     </div>
    <!-- End of Roles and Permissions -->
</body>
</html>