<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$conn = include 'db.php';

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// FOR DATABASE INSERT, UPDATE, DELETE QUERIES

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

  // Enters here when adding a team
  if ($_POST['action'] == 'add') {
    $teamName = ucfirst($_POST['teamName']);
    $teamImage = $_FILES['teamImage'];
    $courses = ""; // Temporary

    // Upload the file (image)
    $imagePath = 'uploads/' . uniqid() . '-' . basename($teamImage['name']);

    if (move_uploaded_file($teamImage['tmp_name'], $imagePath)) {
      $sql = "CALL sp_insertTeam(?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sss", $teamName, $imagePath, $courses);

      if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'New Team added successfully!']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding team: ' . $sql . "<br>" . $conn->error]);
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

    // Updating image
    if ($teamImage['tmp_name']) {
      $imagePath = 'uploads/' . uniqid() . '-' . basename($teamImage['name']);

      if (move_uploaded_file($teamImage['tmp_name'], $imagePath)) {
        $sql = "CALL sp_editTeam(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $teamID, $imagePath, $teamName);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed!']);
      }
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
}

// Handle team deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teamid'])) {
    $teamID = $_POST['teamid'];

    $sql = "CALL sp_delTeam(?)"; 
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

?>