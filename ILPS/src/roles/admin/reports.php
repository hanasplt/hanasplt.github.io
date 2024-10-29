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
          <button>View Overall Score Sheets per Event</button>
        </div>
        <div class="radiobtn-container">
          <input type="radio" name="scoreFilter" id="dailyScores">
          <label for="dailyScores">Daily</label>
          <input type="radio" name="scoreFilter" id="yearlyScore">
          <label for="yearlyScore">Yearly</label>
        </div>
      </div>
      <div class="events-scoresheet-container">
        <div class="table-container">
          <table>
            <tr>
              <th>Event Name</th>
              <th>Team Name</th>
              <th>Points</th>
              <th>Scored At</th>
              <th>Updated At</th>
            </tr>
            <?php
            // Retrieve and display data from database
            $getScoreRecord = "
              SELECT 
                ve.eventName, vt.teamName, vs.total_score, tt.scored_at, tt.updatedscore_at
              FROM vw_trialtr tt
              INNER JOIN vw_subresult vs ON vs.subId = tt.result_id
              INNER JOIN vw_events ve ON ve.eventID = vs.eventId
              INNER JOIN vw_eventparti vp ON vp.contId = vs.contestantId
              INNER JOIN vw_teams vt ON vt.teamId = vp.teamId;
              ";

            $stmt = $conn->prepare($getScoreRecord);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
              // Display rows - scores
              while ($row = $result->fetch_assoc()) {
                echo "
                  <tr>
                    <td>$row[eventName]</td>
                    <td>$row[teamName]</td>
                    <td>$row[total_score]</td>
                    <td>$row[scored_at]</td>
                    <td>$row[updatedscore_at]</td>
                  </tr>
                  ";
              }
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
          </table>
        </div>
      </div>
      <!-- DISPLAY THIS IN OVERALL SCORE SHEET PAGE
    <div class="events-scoresheet-container">
      <div class="table-container">
      <table>
          <tr>
              <th>Event Name</th>
              <th>View Score Sheet</th>
          </tr>
          <?php
          $events = array(); // Array to store all events

          // Retrieve events information
          $sql = "CALL sp_getEvents";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $retval = $stmt->get_result();

          if ($retval->num_rows > 0) {
            while ($row = $retval->fetch_assoc()) {
              $evid = $row['eventID']; // Event ID from the database
              $evname = $row['eventName']; // Event Name from the database
              $catg = $row['eventType']; // Event Type from the database

              // Populate $events array
              $events[] = array('evid' => $evid, 'evname' => $evname, 'type' => $catg);
            }
          } else { // Display message - no events
            echo '
                <tr>
                  <td colspan=2>No event/s exists.</td>
                </tr>
                ';
          }

          $retval->free();
          $stmt->close();

          // Display events and links
          foreach ($events as $ev) {
            $eventId = $ev['evid'];
            $evName = $ev['evname'];
            $evType = $ev['type'];

          ?>
                  <tr>
                    <td><?php echo $evName ?></td>
                <?php

                if ($evType == "Socio-Cultural") {
                  // Checks if the Socio-Cultural Event is scored
                  $sql = "CALL sp_getJudges(?);";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $eventId);
                  $stmt->execute();
                  $retval = $stmt->get_result();

                  // Display clickable links to show scoresheet
                  if ($retval->num_rows > 0) {
                    echo "<td>";
                    echo "<a href='viewScoresheet.php?event=$eventId
                            &&evname=$evName' target='_blank'>View Summary
                          </a>";
                    echo "</td>";
                  } else { // Display message - not scored
                    echo '<td style="color: gray;">No score yet.</td>';
                  }
                  $retval->free();
                  $stmt->close();
                }

                if ($evType == "Sports") {
                  // Checks if the Sports Event is scored
                  $sql = "CALL sp_getScoreSport(?)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $eventId);
                  $stmt->execute();
                  $retval = $stmt->get_result();

                  // Display clickable links to show overall tally
                  if ($retval->num_rows > 0) {
                ?>
                    <td>
                      <a href="viewtally.php?event=<?php echo $eventId ?>
                        &evname=<?php echo $evName ?>" target="_blank">View Tally
                      </a>
                    </td>
                    
                    <?php
                  } else { // Display message - not scored
                    echo '<td style="color: gray;">No score yet.</td>';
                  }
                  $retval->free();
                  $stmt->close();
                }
                echo "</tr>";
              }
                    ?>
      </table>
      </div>

    </div>
     -->
    <?php } else { // Display message - not permitted to view
    echo '
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>FYI: </strong> You lack the permission to view Reports.
      </div>
    ';
  } ?>
    </div>

    <!-- logout confirmation -->
    <script>
      document.getElementById('logout').addEventListener('click', function() {
        Swal.fire({
          title: 'Are you sure?',
          text: "You will be logged out!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#7FD278',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, log me out',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            // mag redirect siya to the login page
            window.location.href = 'reports.php?logout';
          }
        });
      });
    </script>

</body>

</html>