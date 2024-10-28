<?php
require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php'; // database connection
require_once '../admin/verifyLoginSession.php'; // logged in or not
require_once 'adminPermissions.php'; // Retrieves admin permissions
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Log</title>

    <link rel="stylesheet" href="css/accesslog.css">

    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
            <p onclick="window.location.href = 'admin.php';" class="navbar" title="Home">Home</p>
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

    <?php // Display logs - permitted to view
    if (in_array('logs_read', $admin_rights)) { ?>
        <div class="div-table">
            <div class="filter-dropdown">
                <form method="post" id="exportForm">
                    <div class="export-button">
                        <button type="button" onclick="submitForm('export/exportLogXlxs.php')" name="exportlog_xsls" id="exportlog_xsls">Export as Excel</button>
                        <button type="button" onclick="submitForm('export/exportLogpdf.php')" name="exportlog_pdf" id="exportlog_pdf">Export as PDF</button>
                    </div>
                    <!-- Filter Dropdown -->
                    <label for="yearFilter">Filter by Year:</label>
                    <select id="yearFilter" name="yearFilter">
                        <option value="" selected disabled>Year</option>
                    </select>
                </form>
            </div>
            <table id="tableLog">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Date</td>
                        <td>Name</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    <!-- DISPLAY DATA LOG HERE -->
                </tbody>
            </table>
            <div class="msg-container" style="text-align: center;">
                <p id="db-msg"> <!-- DISPLAY MESSAGE HERE --> </p>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div id="pagination">
            <button id="prevBtn" onclick="changePage(currentPage - 1)">Previous</button>
            <span id="pageInfo" ($pageInfo)> </span>
            <button id="nextBtn" onclick="changePage(currentPage + 1)">Next</button>
        </div>

        <script src="js/log.js"></script>
    <?php // Display message - not permitted to view
    } else {
        echo '
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>FYI: </strong> You lack the permission to view the Logs.
            </div>
        ';
    } ?>
</body>

</html>