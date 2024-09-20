<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../admin/verifyLoginSession.php'; // Logged in or not
$conn = require_once '../../../config/db.php'; // Database connection

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Pagination setup
$recordsPerPage = 3;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage); // Ensure the page is always >= 1
$offset = ($currentPage - 1) * $recordsPerPage;

// Retrieve teams with error handling
try {
  // Use prepared statements with the stored procedure
  $sql = "CALL sp_getTeam(?, ?)"; // Limit team display
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    throw new Exception("Prepare failed: " . $conn->error);
  }
  
  $stmt->bind_param("ii", $recordsPerPage, $offset);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if (!$result) {
    throw new Exception("Execute failed: " . $stmt->error);
  }

} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Teams</title>
  <link rel="stylesheet" href="../admin/css/teams.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" href="../../../public/assets/icons/logo.png">
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
            // Escape the output to prevent XSS attacks
            $teamImageSrc = htmlspecialchars($row['image']);
            $teamName = htmlspecialchars($row['teamName']);
            $teamId = (int)$row['teamId'];

            echo "<div class='card' data-id='$teamId' data-name='$teamName' data-image='../../../public/uploads/$teamImageSrc'>";
            echo "<div class='content'>";
            echo "<div class='img'><img src='$teamImageSrc' alt='Team Image'></div>";
            echo "<div class='details'><div class='name'>$teamName</div></div>";
            echo "<div class='media-icons'>";
            echo "<a href='#' onclick='deleteThis($teamId)'><i class='fas fa-trash'></i></a>";
            echo "<a href='#' onclick='openEditModal(this)'><i class='fas fa-pen'></i></a>";
            echo "</div></div></div>";
          }
        } else {
          echo "<p>No teams found.</p>";
        }
        
        // Clean up result and statement
        $result->free();
        $stmt->close();
        ?>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" id="teamPagination">
    <?php
      try {
        // Get the total number of records
        $stmt = $conn->prepare("CALL sp_getTeamCount()");
        $stmt->execute();
        $resultCount = $stmt->get_result();
        $rowCount = $resultCount->fetch_assoc()['total'];
        $totalPages = ceil($rowCount / $recordsPerPage);

        // Generate pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
          echo "<a href='?page=" . htmlspecialchars($i) . "'>$i</a> ";
        }

        $resultCount->free();
        $stmt->close();

      } catch (Exception $e) {
        die("Error: " . $e->getMessage());
      }
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
        <input type="text" id="teamName" name="teamName" maxlength="20" required><br>

        <label for="course1">Courses that comprises the team:</label>
        <!-- COURSES OPTION HERE -->
        <span>
          <input type="checkbox" name="course[]" id="course1" value="BSIT">
          Bachelor of Science in Information Technology
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course2" value="BEED">
          Bachelor of Elementary Education
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course3" value="BSNED">
          Bachelor of Special Needs Education
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course4" value="BECED">
          Bachelor of Early Childhood
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course5" value="BSED-English">
          Bachelor of Secondary Education - English
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course6" value="BSED-Mathematics">
          Bachelor of Secondary Education - Mathematics
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course7" value="BSED-Filipino">
          Bachelor of Secondary Education - Filipino
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course8" value="BTVTED">
          Bachelor of Technical-Vocational Teacher Education
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course9" value="BSABE">
          Bachelor of Science in Agricultural and Biosystems Engineering
        </span>
        

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
        <input type="text" id="editTeamName" name="teamName" maxlength="20" required><br>

        <label for="course1">Courses that comprises the team:</label>
        <!-- COURSES OPTION HERE -->
        <span>
          <input type="checkbox" name="course[]" id="course1" value="BSIT">
          Bachelor of Science in Information Technology
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course2" value="BEED">
          Bachelor of Elementary Education
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course3" value="BSNED">
          Bachelor of Special Needs Education
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course4" value="BECED">
          Bachelor of Early Childhood
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course5" value="BSED-English">
          Bachelor of Secondary Education - English
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course6" value="BSED-Mathematics">
          Bachelor of Secondary Education - Mathematics
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course7" value="BSED-Filipino">
          Bachelor of Secondary Education - Filipino
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course8" value="BTVTED">
          Bachelor of Technical-Vocational Teacher Education
        </span>
        <span>
          <input type="checkbox" name="course[]" id="course9" value="BSABE">
          Bachelor of Science in Agricultural and Biosystems Engineering
        </span>

        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeModal('editModal')">Cancel</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../admin/js/teams.js"></script>
</body>
</html>
