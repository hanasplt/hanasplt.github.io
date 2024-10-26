<?php
require_once '../../../config/sessionConfig.php'; // Session Cookie
require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not
require_once 'adminPermissions.php'; // Retrieves admin permissions

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

  $numTeams = $result->num_rows; // number of teams
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

  <!-- Sweet Alert -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!--Web-logo-->
  <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

  <!--font-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

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
      <p onclick="window.location.href = 'teams.php';" class="navbarbie" title="Teams">Teams</p>
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
  <?php if (in_array('team_read', $admin_rights)) { ?>
    <div class="container">
      <div class="dash-banner">
        <div class="banner">
          <div class="banner-left">
            <h1 class="intrams-name">INDIGAY 2024</h1>
            <p class="intrams-theme">Uniting Through Talent, Embracing Diversity, and Empowering Inclusivity Within The Community</p>
          </div>
          <div class="banner-right">
            <img src="../../../public/assets/icons/banner-4.png">
          </div>
        </div>
      </div>
      <div class="main-card">
        <?php if (in_array('team_add', $admin_rights)) { ?>
          <div class="new-team" onclick="openAddModal()">
            <div class="plus-icon">
              <i class="fas fa-plus"></i>
            </div>
            <div class="new-team-info">
              <p id="create">Create a New Team</p>
              <p id="add">Add a new team for intramurals.</p>
            </div>
          </div>
        <?php } else {
          echo '
                  <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong>FYI!</strong> \'Add Team\' feature is hidden as you don\'t have the permission.
                  </div>
                ';
        } ?>
        <div class="team-container">
          <p id="teams">Teams</p>
          <div class="team-profile">
            <div class="cards" id="cardContainer">
              <?php
              if ($result->num_rows > 0) {
                $teams = [];
                while ($row = $result->fetch_assoc()) {
                  $teams[] = $row;
                }

                $numTeams = count($teams);
                $emptyCards = 3 - $numTeams;

                echo "<div class='row'>";
                foreach ($teams as $team) {
                  $teamImageSrc = htmlspecialchars($team['image']);
                  $teamName = htmlspecialchars($team['teamName']);
                  $teamId = (int)$team['teamId'];

                  echo "<div class='card' id='openTeam' data-id='$teamId' data-name='$teamName' data-image='../../../public/uploads/$teamImageSrc'>";
                  echo "<div class='content'>";
                  echo "<div class='img'><img src='$teamImageSrc' alt='Team Image'></div>";
                  echo "<div class='details'><div class='name'>$teamName</div></div>";
                  echo "<div class='media-icons'>";

                  if (in_array('team_update', $admin_rights)) {
                    echo "<button href='#' class='delete-btn' data-id='$teamId' data-name='$teamName'><i class='fas fa-trash'></i></button>";
                  }
                  if (in_array('team_delete', $admin_rights)) {
                    echo "<a href='#' class='editteam-btn' data-id='$teamId' data-name='$teamName'><i class='fas fa-pen'></i></a>";
                  }
                  echo "</div></div></div>";
                }

                for ($i = 0; $i < $emptyCards; $i++) {
                  echo "<div class='card empty-card'></div>";
                }

                echo "</div>";
              } else {
                echo "<p>No teams found.</p>";
              }

              $result->free();
              $stmt->close();
              ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination Controls -->
      <?php if ($numTeams > 0) { ?>
        <div class="pagination">
          <?php
          try {
            $stmt = $conn->prepare("CALL sp_getTeamCount()");
            $stmt->execute();
            $resultCount = $stmt->get_result();
            $rowCount = $resultCount->fetch_assoc()['total'];
            $totalPages = ceil($rowCount / $recordsPerPage);

            if ($currentPage > 1) {
              echo "<a href='?page=" . ($currentPage - 1) . "' class='arrow'>&laquo; previous</a>";
            } else {
              echo "<a href='#' class='arrow disabled'>&laquo; previous</a>";
            }

            for ($i = 1; $i <= $totalPages; $i++) {
              $activeClass = ($i == $currentPage) ? 'active' : '';
              echo "<a href='?page=" . htmlspecialchars($i) . "' class='$activeClass'>$i</a> ";
            }

            if ($currentPage < $totalPages) {
              echo "<a href='?page=" . ($currentPage + 1) . "' class='arrow'>next &raquo;</a>";
            } else {
              echo "<a href='#' class='arrow disabled'>next &raquo;</a>";
            }

            $resultCount->free();
            $stmt->close();
          } catch (Exception $e) {
            die("Error: " . $e->getMessage());
          }
          ?>
        </div>
      <?php } ?>

      <!-- Add Modal -->
      <div id="addModal" class="modal-add">
        <div class="modal-content-add">
          <span class="close" onclick="closeModal('addModal')">&times;</span>
          <h2 class="addnew">Add New Team</h2>
          <form action="" id="teamForm" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <label for="teamImage">Team Image</label>
            <input type="file" id="teamImage" name="teamImage" accept="image/*" required><br><br>
            <label for="teamName">Team Name</label>
            <input type="text" id="teamName" name="teamName" maxlength="20" required><br>

            <label for="course1">Courses that comprises the team</label>
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
            <label for="editTeamImage">Team Image</label>
            <input type="file" id="editTeamImage" name="teamImage" accept="image/*"><br><br>
            <label for="editTeamName">Team Name</label>
            <input type="text" id="editTeamName" name="teamName" maxlength="20" required><br>

            <label for="course1">Courses that comprises the team</label>
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
    <?php } else {
    echo '
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Oops!</strong> You lack the permission to view \'Teams\' features.
        </div>
      ';
  } ?>
</body>

</html>