<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = require_once '../../../config/db.php'; // Database connection

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// HANDLE TEAM INSERTION AND/OR UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

  // Enters here when adding a team
  try {
    if ($_POST['action'] == 'add') {
      $teamName = ucfirst($_POST['teamName']);
      $teamImage = $_FILES['teamImage'];
      $courses = $_POST['course'];


      // Upload the file (image)
      $imagePath = '../../../public/uploads/' . uniqid() . '-' . basename($teamImage['name']);
  
      if (move_uploaded_file($teamImage['tmp_name'], $imagePath)) {
  
        $values = [];
        foreach ($courses as $course) {
          $values[] = $conn->real_escape_string($course); // Prevent SQL injection
        }

        $courseValues = implode(", ", $values);
  
        $sql = "CALL sp_insertTeam(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $teamName, $courseValues, $imagePath);
  
        if ($stmt->execute()) {
          echo json_encode(['status' => 'success', 'message' => 'New Team added successfully!']);
        } else {
          echo json_encode(['status' => 'error', 'message' => 'Error adding team!']);
        }
      } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed!']);
      }
      $stmt->close();
      exit;
    }
  
  
    // Enters here when editing a team
    if ($_POST['action'] == 'edit') {
      $teamID = $_POST['teamID'];
      $teamName = ucfirst($_POST['teamName']);
      $teamImage = $_FILES['teamImage'];
      $courses = $_POST['course'];
  
      // Updating image
      if ($teamImage['tmp_name']) {
        $imagePath = '../../../public/uploads/' . uniqid() . '-' . basename($teamImage['name']);

        if (move_uploaded_file($teamImage['tmp_name'], $imagePath)) {
          // Image is changed

          $values = [];
          foreach ($courses as $course) {
            $values[] = $conn->real_escape_string($course); // Prevent SQL injection
          }

          $courseValues = implode(", ", $values);

          $sql = "CALL sp_editTeam(?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("isss", $teamID, $teamName, $courseValues, $imagePath);
        } else {
          echo json_encode(['status' => 'error', 'message' => 'File upload failed!']);
        }
      } else {
        // Only name and/or members changed, no image
        
        $values = [];
        foreach ($courses as $course) {
          $values[] = $conn->real_escape_string($course); // Prevent SQL injection
        }

        $courseValues = implode(", ", $values);

        $sql = "CALL sp_editTeamName(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $teamID, $teamName, $courseValues);
      }
  
      if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Team updated successfully!']);
      } else {
        echo json_encode(['status' => 'error', 'message' => "Unable to update team."]);
      }
  
      $stmt->close();
      exit;
    }
  } catch (Exception $e) {
    die("Error: " . $e->getMessage());
  }

}

// Handle team deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teamid'])) {
    $teamID = $_POST['teamid'];

    $sql = "CALL sp_delTeam(?)"; // This doesn't really delete the team (FK constraint)
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teamID);

    $response = array();

    if ($stmt->execute()) {
      $response['success'] = true;
    } else {
      $response['success'] = false;
      $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Retrieve team to edit
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['editID'])) {
  $teamid = $_GET['editID'];

  try {
    // Retrieve the courses in the database
    $getCourses = "CALL sp_getATeam(?)";

    $stmt = $conn->prepare($getCourses);
    $stmt->bind_param("i", $teamid);
    $stmt->execute();

    $retval = $stmt->get_result();

    $row = $retval->fetch_assoc();
    $courses = explode(', ', $row['members']); // Separate comma-separated string

    echo json_encode(['courses' => $courses]);

  } catch (Exception $e) {
    die("Error: " . $e->getMessage());
  }
}

?>