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
  <link rel="icon" href="../../../public/assets/icons/logo.png">
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
      <p onclick="window.location.href = 'reports.php';"><b>Reports</b></p>
      <p onclick="window.location.href = '../admin/logs/accesslog.html';">Logs</p>
    </div>
    <div class="menu-icon">
      <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
    </div>
  </div>
  <?php // Display reports - permitted to view 
  if (in_array('reports_read', $admin_rights)) {?>
  <div class="events-scoresheet-container" style="margin-top: 10%;">
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
              while($row = $retval->fetch_assoc()) {
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
            foreach($events as $ev) {
              $eventId = $ev['evid'];
              $evName = $ev['evname'];
              $evType = $ev['type'];

              ?>
                <tr>
                  <td><?php echo $evName ?></td>
              <?php

              if($evType == "Socio-Cultural") {
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

              if($evType == "Sports") {
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
  <?php } else { // Display message - not permitted to view
  echo '
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <strong>FYI: </strong> You lack the permission to view Reports.
    </div>
  ';
  }?>

  <!-- logout confirmation -->
  <script>
        document.getElementById('logoutIcon').addEventListener('click', function() {
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