<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ilpsystem";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['action']) && $_POST['action'] == 'add') {
      $partID = $_POST['partID'];
      $partName = $_POST['partName'];
      $teamName = $_POST['teamName'];
      $event = $_POST['Event'];

      $sql = "CALL sp_getParticipant(?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $partID);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
          echo "<script>alert('Participant already exists');</script>";
          $result->free();
          $stmt->close();
      } else {
          $result->free();
          $stmt->close();

          $sql = "CALL sp_getEvent(?)";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $event);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $eventname = $row['eventName'];
              $eventType = $row['eventType'];

              $result->free();
              $stmt->close();

              $sql = "CALL sp_addPart(?, ?, ?, ?, ?)";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("issss", $partID, $partName, $teamName, $eventname, $eventType);
              if ($stmt->execute()) {
                  echo "<script>alert('New participant added');</script>";
              } else {
                  echo "<script>alert('Error: Unable to add participant');</script>";
              }
              $stmt->close();
          } else {
              echo "<script>alert('Event does not exist');</script>";
              $result->free();
              $stmt->close();
          }
      }
  }
}

$recordsPerPage = 3;

$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

$sql = "CALL sp_getTeam(?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recordsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ILPS</title>
    <link rel="stylesheet" href="/assets/css/faci.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/icons/logo-1.png">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>FACILITATOR</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.html';" hidden>Home</p>
            <p onclick="window.location.href = 'accounts.html';" hidden>Accounts</p>
            <p onclick="window.location.href = 'create-team.html';" hidden>Teams</p>
            <p onclick="window.location.href = 'EventTeam.html';" hidden>Events</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" style="cursor: pointer;" onclick="window.location.href = 'landing-page.html';"></i>
        </div>
    </div>

    <div class="container">
        <div class="main-card">
            <div class="cards" id="cardContainer">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='card' data-id='" . $row['teamId'] . "' data-name='" . $row['teamName'] . "' data-image='data:image/jpeg;base64," . base64_encode($row['image']) . "'>";
                        echo "<a onclick=\"openAddModal('" . $row['teamName'] . "');\">";
                        echo "<div class='content'>";
                        echo "<div class='img'>";
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='Team Image'>";
                        echo "</div>";
                        echo "<div class='details'>";
                        echo "<div class='name' name='TeamName'>" . $row['teamName'] . "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "<a href=''>";
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
            $stmt = $conn->prepare("CALL sp_getTeamCount()");
            $stmt->execute();
            $resultCount = $stmt->get_result();
            $rowCount = $resultCount->fetch_assoc()['total'];
            $totalPages = ceil($rowCount / $recordsPerPage);
            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='?page=$i'>$i</a>";
            }

            $resultCount->free();
            $stmt->close();
            ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal-add">
        <div class="modal-content-add">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2 class="addnew">Add New Participants</h2>
            <form id="teamForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <label for="partID">Participants ID:</label>
                <input type="number" id="partID" name="partID" required><br><br>
                <label for="partName">Participants Name:</label>
                <input type="text" id="partName" name="partName" required><br><br>
                <label for="teamName">Faction:</label>
                <input type="text" id="teamName" name="teamName" value="" readonly><br><br>
                <label for="Event">Event:</label>
                <select class="Event" id="Event" name="Event">
                    <?php
                    $stmt = $conn->prepare("CALL sp_getEvents()");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $eventval = $row['eventID'];
                            $eventName = $row['eventName'];
                            echo "<option value='$eventval'>$eventName</option>";
                        }
                    }
                    $result->free();
                    $stmt->close();
                    ?>
                </select><br><br>
                <button type="submit" name="addparticipant">Add Participants</button>
                <button type="button" onclick="closeModal('addModal')">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal(teamName) {
            document.getElementById('teamName').value = teamName;
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>

    <?php $conn->close(); ?>
</body>

</html>
