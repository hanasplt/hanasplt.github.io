<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
$conn = require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Teams</title>
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





  <div class="events-scoresheet-container" style="margin-top: 10%;">
    <div class="table-container">
    <table>
        <tr>
            <th>Event Name</th>
            <th>View Score Sheet</th>
        </tr>
        <?php
            $events = array();
            $links = array();

            #retrieve event name
            $sql = "CALL sp_getEvents";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $retval = $stmt->get_result();

            if ($retval->num_rows > 0) {
              while($row = $retval->fetch_assoc()) {
                $evid = $row['eventID'];
                $evname = $row['eventName'];
                $catg = $row['eventType'];

                $events[] = array('evid' => $evid, 'evname' => $evname, 'type' => $catg);
              }
            }

            $retval->free();
            $stmt->close();


            foreach($events as $ev) {
              $eventId = $ev['evid'];
              $evName = $ev['evname'];
              $evType = $ev['type'];

              ?>
                <tr>
                  <td><?php echo $evName ?></td>
              <?php

              if($evType == "Socio-Cultural") {
                #display judges
                $sql = "CALL sp_getJudges(?);";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $eventId);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                  $pernum = 0;
                  echo "<td>";
                  echo "<a href='viewScoresheet.php?event=$eventId
                          &&evname=$evName' target='_blank'>View Summary
                        </a>";
                  echo "</td>";
                }
                $retval->free();
                $stmt->close();
              }

              if($evType == "Sports") {
                $sql = "CALL sp_getScoreSport(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $eventId);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                  ?>
                  <td>
                    <a href="viewtally.php?event=<?php echo $eventId ?>
                      &evname=<?php echo $evName ?>" target="_blank">View Tally
                    </a>
                  </td>
                  
                  <?php
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








  <script>
    function openAddModal() {
      var modal = document.getElementById("addModal");
      modal.style.display = "block";
    }

    function openEditModal(element) {
      var card = element.closest('.card');
      var teamID = card.getAttribute('data-id');
      var teamName = card.getAttribute('data-name');
      var teamImage = card.getAttribute('data-image');

      document.getElementById('editTeamID').value = teamID;
      document.getElementById('editTeamName').value = teamName;
      document.getElementById('editTeamImage').value = '';

      var modal = document.getElementById("editModal");
      modal.style.display = "block";
    }

    function closeModal(modalId) {
      var modal = document.getElementById(modalId);
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      var addModal = document.getElementById("addModal");
      var editModal = document.getElementById("editModal");
      if (event.target == addModal) {
        addModal.style.display = "none";
      } else if (event.target == editModal) {
        editModal.style.display = "none";
      }
    };

    function submitEditForm() {
      var formData = new FormData(document.getElementById('editTeamForm'));
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '', true);
      xhr.onload = function () {
        if (xhr.status === 200) {
          var response = JSON.parse(xhr.responseText);
          if (response.status === 'success') {
            alert(response.message);
            location.reload();
          } else {
            alert(response.message);
          }
        }
      };
      xhr.send(formData);
    }

    function deleteThis(id) {
      window.location.href = 'teams.php?teamid='+id;
    }
  </script>

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
                    window.location.href = 'index.html';
                }
            });
        });
  </script>

</body>

</html>