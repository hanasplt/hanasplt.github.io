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
    <form method="post" enctype="multipart/form-data" class="StreetDanceForm" id="criteriaForm">
    </form>

    <script>
        function loadEventCriteria(eventId) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('criteriaForm').innerHTML = this.responseText;
                    document.getElementById('criteriaForm').style.display = 'block';
                    //addCriteriaFormEventListener();
                }
            };
            xhttp.open("POST", "get_eventFrom copy.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("evid=" + eventId);
        }

        function calculateTotal(criInput) {
            var criteriaInputs = document.getElementsByClassName('criteriaInput'+criInput);
            var totalScore = 0;

            for (var i = 0; i < criteriaInputs.length; i++) {
                var input = criteriaInputs[i];
                if (input.value === '') {
                    return;
                }
                var score = parseInt(input.value);
                var max = parseInt(input.getAttribute('max'));

                if (score > max) {
                    input.value = "";
                    Swal.fire({
                        title: "Oops!",
                        text: "You have exceeded the maximum score.",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }

                totalScore += score;
            }

            document.getElementById('totalScore'+criInput).value = totalScore;

            // After calculating the total, check if all scores are equal
            var areScoresEqual = checkIfScoresAreEqual();
            if (areScoresEqual) {
                Swal.fire({
                    title: "Oops!",
                    text: "There's a tie! Please break the tie by changing the score.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }
        }

        // Validate if there is a tie in the total score
        function checkIfScoresAreEqual() {
            var totalScoreElements = document.querySelectorAll('[id^="totalScore"]');
            
            if (totalScoreElements.length === 0) {
                return; // No totalScore inputs found
            }
            
            // Get the first total score to compare with the others
            var firstScore = parseInt(totalScoreElements[0].value);
            
            // Iterate through all total scores and compare them
            for (var i = 1; i < totalScoreElements.length; i++) {
                var currentScore = parseInt(totalScoreElements[i].value);
                
                // If the current score is not equal to the first one, return false
                if (currentScore !== firstScore) {
                    return false;
                } else {
                    return true; // If a score are similar, return true
                }
            }
        }

        document.getElementById('criteriaForm').addEventListener('submit', function(event) {
            var inputs = document.querySelectorAll('.criteriaInput');
            var total = 0;
            inputs.forEach(function(input) {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalScore').value = total;
        });
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


    <?php
        if(isset($_GET['event'])) { // When 'event' is set, criteria will be loaded
            $evId = $_GET['event'];
            echo "<script>loadEventCriteria($evId)</script>";
        }
    ?>
</body>

</html>
