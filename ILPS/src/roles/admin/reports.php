<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not
require_once 'adminPermissions.php'; // Retrieves admin permissions

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <link rel="stylesheet" href="../admin/css/report.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

  <!--Web-logo-->
  <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

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
          <p onclick="window.location.href = 'view-profile.php';" class="dc-text" title="Profile">View Profile</p>
          <p onclick="window.location.href = 'reports.php';" class="dc-text" title="Reports">Reports</p>
          <p onclick="window.location.href = 'accesslog.php';" class="dc-text" title="Logs">Logs</p>
          <p onclick="showConfirmationMsg()" class="dc-text" title="Backup">Backup and Reset</p>
          <div class="menu-icon">
            <p id="logout" title="Logout">Logout</p>
          </div>
        </div>
      </div>
    </nav>
  </div>
  <?php // Display reports - permitted to view 
  if (in_array('reports_read', $admin_rights)) { ?>
    <div class="reports-container">
      <div class="overall-score-btn-container">
        <div class="overall-btn-container">
          <button id="score_sheets">View Overall Score Sheets per Event</button>
        </div>
        <div class="export-btn-container">
          <form method="post" id="exportForm">
            <button type="button" onclick="submitForm('export/exportReportXlxs.php')" name="exportreport_xsls" id="exportlog_xsls">
              Export as Excel
            </button>
            <button type="button" onclick="submitForm('export/exportReportpdf.php')" name="exportreport_pdf" id="exportlog_pdf">
              Print PDF
            </button>
          </form>

        </div>
        <div class="dropdown-container">
          <select name="eventFilter" id="eventFilter" onchange="filterTable()">
            <option value="all">All</option>
            <?php
              // Retrieve events - has score record
              $getEvents = "CALL sp_getScoredEvent";

              $stmt = $conn->prepare($getEvents);
              $stmt->execute();
              $ret_events = $stmt->get_result();
              
              if ($ret_events->num_rows > 0) {
                // Populate options with events
                while ($row = $ret_events->fetch_assoc()) {
                  echo "
                    <option value='$row[eventName]'>$row[eventName]</option>
                  ";
                }
              }

              $ret_events->free();
              $stmt->close();
            ?>
          </select>
          <select name="filterOpt" id="filterOpt" onchange="filterTable()">
            <option value="today">Today</option>
            <option value="all">All (Current Year)</option>
          </select>
        </div>
      </div>
      <div class="events-scoresheet-container">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Event Name</th>
                <th>Team Name</th>
                <th>Points</th>
                <th>Action</th>
                <th>Action At</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <?php
              // Retrieve and display data from database
              $getScoreRecord = "CALL sp_scoreReport()";

              $stmt = $conn->prepare($getScoreRecord);
              $stmt->execute();
              $result = $stmt->get_result();

              $dataArray = []; // Initialize array

              if ($result->num_rows > 0) {
                // Display rows - scores
                while ($row = $result->fetch_assoc()) {
                  echo '
                    <tr class="event-row">
                      <td>'.$row['eventName'].'</td>
                      <td>'.$row['teamName'].'</td>
                      <td>'.$row['total_score'].'</td>
                      <td>'.$row['action_made'].'</td>
                      <td>'.$row['action_at'].'</td>
                    </tr>
                  ';
                  
                  // Store these datas in the array
                  $dataArray[] = [
                    'eventName' => $row['eventName'],
                    'teamName' => $row['teamName'],
                    'total_score' => $row['total_score'],
                    'action_made' => $row['action_made'],
                    'action_at' => $row['action_at']
                  ];
                }

                // Output the data array as JSON
                echo "<script>const data = " . json_encode($dataArray) . ";</script>";
              } else {
                // Display message - No Scores added/updated
                echo '
                  <tr>
                    <td colspan=5>No Scores were added.</td>
                  </tr>
                  ';
              }

              $result->free();
              $stmt->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Pagination Controls -->
      <div id="pagination" class="pagination"></div>
    <?php } else { // Display message - not permitted to view
    echo '
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>FYI: </strong> You lack the permission to view Reports.
      </div>
    ';
  } ?>
    </div>

    <script src="js/reports.js"></script>

</body>

</html>