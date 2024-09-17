<?php
session_start();

$conn = include 'db.php'; // Include Database Connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['judgeId'];


// Record the Score Sheet (tbc)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'record') {

    $event = $_POST['evName']; // Event Name
    $parti = $_POST['contestant']; // Another pending, should be the same as criteria is inserted
    $total = $_POST['totalScore']; // So is the totalScore
    $criteria_scores = $_POST['criteria'];

    $criteria_values = array_fill(0, 10, 0);

    $index = 0;
    foreach ($criteria_scores as $criteria => $score) {
        if ($index < 10) {
            $criteria_values[$index] = $score;
            $index++;
        }
    }

    $sql = "CALL sp_insertResult(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $params = array_merge([$event, $parti, $id, $total], $criteria_values);
    $types = "iisd" . str_repeat("d", count($criteria_values));
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo '<script>alert("Recorded!");</script>';

        $sql = "CALL sp_updateContStat(?)";
        $prep = $conn->prepare($sql);
        $prep->bind_param("i", $parti);
        $result = $prep->execute();

        if (!$result) {
            echo '<script>alert("Failed to update status!");</script>';
            error_log("Error updating status: " . $conn->error);
        }
    } else {
        echo '<script>alert("Failed to record!");</script>';
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="assets/css/SCevents.css">
    <link rel="icon" href="assets/icons/logo.svg">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="assets/icons/logoo.png" alt="Logo">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>JUDGE</i></p>
        </div>

        <div class="links">
            <p onclick="window.location.href = 'admin.html';" hidden>Home</p>
            <p onclick="window.location.href = 'accounts.html';" hidden>Accounts</p>
            <p onclick="window.location.href = 'create-team.html';" hidden>Teams</p>
            <p onclick="window.location.href = 'EventTeam.html';" hidden>Events</p>
        </div>

        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>


    <!-- GENERAL CRITERIA -->
    <form method="post" enctype="multipart/form-data" id="criteriaForm">
    </form>

    <script src="SCevents.js"></script>

    <?php
        if(isset($_GET['event'])) { // When 'event' is set, criteria will be loaded
            $evId = $_GET['event'];
            echo "<script>loadEventCriteria($evId)</script>";
        }
    ?>
</body>

</html>
