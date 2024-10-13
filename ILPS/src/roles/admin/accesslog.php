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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="icon" href="../../../public/assets/icons/logo.svg">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>ADMINISTRATOR</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.php';">Home</p>
            <p onclick="window.location.href = 'accounts.php';">Accounts</p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';">Events</p>
            <p onclick="window.location.href = 'schedule.php';">Schedule</p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
            <p onclick="window.location.href = 'accesslog.php';"><b>Logs</b></p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>

    <?php // Display logs - permitted to view
    if (in_array('logs_read', $admin_rights)) {?>
    <div class="div-table">
        <div class="filter-dropdown">
            <form method="post" id="exportForm">
                <div class="export-button">
                    <button type="button" onclick="submitForm('export/exportLogXlxs.php')" name="exportlog_xsls" id="exportlog_xsls">Export to Excel</button>
                    <button type="button" onclick="submitForm('export/exportLogpdf.php')" name="exportlog_pdf" id="exportlog_pdf">Export to PDF</button>
                </div>

            <!-- Filter Dropdown -->
            <label for="yearFilter">Filter by Year:</label>
            <select id="yearFilter" name="yearFilter">
                <option value="" selected disabled>--Select Year--</option>
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