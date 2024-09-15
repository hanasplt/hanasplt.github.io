<?php
$conn = include 'db.php';

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Pagination setup
$recordsPerPage = 3;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Retrieve teams
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="titlename">
        <b>INTRAMURALS 2024</b>
        </div>
      <button class="addteam" onclick="openAddModal()">ADD TEAM</button>
      <div class="cards" id="cardContainer">
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $teamImageSrc = $row['image']; 
            echo "<div class='card' data-id='" . $row['teamId'] . "' data-name='" . $row['teamName'] . "' data-image='" . $teamImageSrc . "'>";
            echo "<div class='content'>";
            echo "<div class='img'>";
            echo "<img src='" . $teamImageSrc . "' alt='Team Image'>"; 
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
      <form action="" id="teamForm" method="post" enctype="multipart/form-data">
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
        <input type="text" id="editTeamName" name="teamName" maxlength="20" required><br><br>
        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeModal('editModal')">Cancel</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="teams.js"></script>
</body>
</html>
