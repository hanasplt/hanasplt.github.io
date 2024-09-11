<?php
  $conn = include 'db.php';

  //add team
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $teamName = ucfirst($_POST['teamName']);
    $teamImage = $_FILES['teamImage']['tmp_name'];
    $teamImageContent = addslashes(file_get_contents($teamImage));
    
    $sql = "CALL sp_insertTeam(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $teamName, $teamImageContent);
    if ($stmt->execute()) {
        echo '<script>alert("New team added successfully!");</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
  }

  //edit team
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $teamID = $_POST['teamID'];
    $teamName = ucfirst($_POST['teamName']);

    if (isset($_FILES['teamImage']) && $_FILES['teamImage']['tmp_name']) {
        $teamImage = $_FILES['teamImage']['tmp_name'];
        $teamImageContent = addslashes(file_get_contents($teamImage));
        $sql = "CALL sp_editTeam(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $teamID, $teamImageContent, $teamName);
    } else {
        $sql = "CALL sp_editTeamName(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $teamID, $teamName);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Team updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error: " . $sql . "<br>" . $conn->error]);
    }
    $stmt->close();
    exit;
  }

  //delete team
  if (isset($_GET['teamid'])) {
    $teamID = $_GET['teamid'];
    
    $sql = "CALL sp_delTeam(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teamID);

    if ($stmt->execute()) {
      echo "<script>alert('Team deleted successfully!'); window.location.href='teams.php';</script>";
    }
    $stmt->close();
  }

  $recordsPerPage = 3;

  if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = $_GET['page'];
  } else {
    $currentPage = 1;
  }

  $offset = ($currentPage - 1) * $recordsPerPage;

  //retrieve teams
  $sql = "CALL sp_getTeam(?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $recordsPerPage, $offset);
  $stmt->execute();
  $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Teams</title>
  <link rel="stylesheet" href="assets/css/teams.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="assets/icons/logo.png">
</head>

<body>
  <div class="nav-bar">
    <img class="logo-img" src="assets/icons/logoo.png">
    <div class="logo-bar">
      <p>Intramural Leaderboard</p>
      <p>and Points System</p>
      <p id="administrator"><i>ADMINISTRATOR</i></p>
    </div>
    <div class="links">
      <p onclick="window.location.href = 'admin.php';">Home</p>
      <p onclick="window.location.href = 'accounts.php';">Accounts</p>
      <p onclick="window.location.href = 'teams.php';"><b>Teams</b></p>
      <p onclick="window.location.href = 'EventTeam.php';">Events</p>
      <p onclick="window.location.href = '#';">Schedule</p>
      <p onclick="window.location.href = 'reports.php';">Reports</p>
    </div>
    <div class="menu-icon">
      <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
    </div>
  </div>

  <div class="container">
    <div class="main-card" style="padding: 5%;">
      <button class="addteam" onclick="openAddModal()">ADD TEAM</button>
      <div class="cards" id="cardContainer">
        <?php
        if ($result->num_rows > 0) {
        $teamCount = 0;
          while ($row = $result->fetch_assoc()) {
            $teamCount++;
            echo "<div class='card' data-id='" . $row['teamId'] . "' data-name='" . $row['teamName'] . "' data-image='data:image/jpeg;base64," . base64_encode($row['image']) . "'>";
            echo "<div class='content'>";
            echo "<div class='img'>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='Team Image'>";
            echo "</div>";
            echo "<div class='details'>";
            echo "<div class='name'>" . $row['teamName'] . "</div>";
            echo "</div>";
            echo "<div class='media-icons'>";
            echo "<a href='#' onclick='deleteThis(" . $row['teamId'] . ")'><i class='fas fa-trash'></i></a>";
            echo "<a href='#' onclick='openEditModal(this)'><i class='fas fa-pen'></i></a>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
          }
        } else {
          echo "0 results";
        }
        $result->free();
        $stmt->close();
        ?>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <?php
      $stmt = $conn->prepare("CALL sp_getTeamCount");
      $stmt->execute();
      $resultCount = $stmt->get_result();
      $rowCount = $resultCount->fetch_assoc()['total'];
      $totalPages = ceil($rowCount / $recordsPerPage);
      for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='?page=$i'>$i</a>";
      }
      
      $resultCount->free();
      $stmt->close();
      $conn->close();
      ?>
    </div>
  </div>

  <!-- Add Modal -->
  <div id="addModal" class="modal-add">
    <div class="modal-content-add">
      <span class="close" onclick="closeModal('addModal')">&times;</span>
      <h2 class="addnew">Add New Team</h2>
      <form id="teamForm" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <label for="teamImage">Team Image:</label>
        <input type="file" id="teamImage" name="teamImage" accept="image/*" required><br><br>
        <label for="teamName">Team Name:</label>
        <input type="text" id="teamName" name="teamName" maxlength="20" required><br><br>
        <button type="submit">Add Team</button>
        <button type="button" onclick="closeModal('addModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal-edit">
    <div class="modal-content-edit">
      <span class="close" onclick="closeModal('editModal')">&times;</span>
      <h2 class="addnew">Edit Team</h2>
      <form id="editTeamForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="editTeamID" name="teamID">
        <label for="editTeamImage">Team Image:</label>
        <input type="file" id="editTeamImage" name="teamImage" accept="image/*"><br><br>
        <label for="editTeamName">Team Name:</label>
        <input type="text" id="editTeamName" name="teamName" maxlength="20" required><br><br> <!-- length ani ug sa add -->
        <button type="button" onclick="submitEditForm()">Save Changes</button>
        <button type="button" onclick="closeModal('editModal')">Cancel</button>
      </form>
    </div>
  </div>

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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      console.log(teamID);

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
              try {
                  var response = JSON.parse(xhr.responseText);

                  if (response.status === 'success') {
                      alert(response.message);
                      location.reload();
                  } else {
                      alert('Error: ' + response.message);
                  }
              } catch (e) {
                  console.log('An error occurred while processing the response: ' + e.message);
              }
          } else {
              // Server returned a status other than 200
              alert('Server error: ' + xhr.status + ' ' + xhr.statusText);
          }
      };

      xhr.send(formData);
    }


    function deleteThis(id) {
      Swal.fire({
          title: 'Confirm',
          text: "Do you want to delete this team?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#8F8B8B',
          cancelButtonColor: '#7FD278',
          confirmButtonText: 'Yes'
      }).then((result) => {
          if (result.isConfirmed) {
              // Redirect to delete the team
              window.location.href = 'teams.php?teamid=' + id;
          }
      });
    }
  </script>
</body>

</html>
