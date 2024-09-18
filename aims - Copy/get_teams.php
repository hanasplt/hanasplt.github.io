<?php

$conn = include 'db.php';

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve teams with error handling
    try {

        // tbc 
        // Pagination setup
        $recordsPerPage = 3;
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $currentPage = max(1, $currentPage); // Ensure the page is always more than 1
        $offset = ($currentPage - 1) * $recordsPerPage;

        
        $sql = "CALL sp_getTeam(?, ?)"; // Limit team display
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ii", $recordsPerPage, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $teamImageSrc = htmlspecialchars($row['image']);
              $teamName = htmlspecialchars($row['teamName']);
              $teamId = (int)$row['teamId'];
      
              echo "<div class='card' data-id='$teamId' data-name='$teamName' data-image='$teamImageSrc'>";
              echo "<div class='content'>";
              echo "<div class='img'><img src='$teamImageSrc' alt='Team Image'></div>";
              echo "<div class='details'><div class='name'>$teamName</div></div>";
              echo "<div class='media-icons'>";
              echo "<a href='#' onclick='deleteThis($teamId)'><i class='fas fa-trash'></i></a>";
              echo "<a href='#' onclick='openEditModal(this)'><i class='fas fa-pen'></i></a>";
              echo "</div></div></div>";
            }
        } else {
            echo "No team found.";
        }
      
          $result->free();
          $stmt->close();



        // Get total count of the record
        $stmt = $conn->prepare("CALL sp_getTeamCount");

        $stmt->execute();
        $resultCount = $stmt->get_result();
        $rowCount = $resultCount->fetch_assoc()['total'];
        $totalPages = ceil($rowCount / $recordsPerPage);

        for ($i = 1; $i <= $totalPages; $i++) {
          echo "<a href='?page=" . htmlspecialchars($i) . "'>$i</a> ";
        }
        $resultCount->free();
        $stmt->close();
    
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
    
?>