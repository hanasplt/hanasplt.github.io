<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilps";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
$id = $_SESSION['judgeId'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'record') {
    $event = $_POST['evName'];
    $parti = $_POST['nameSelect'];
    $jid = $_SESSION['judgeId'];
    $total = $_POST['totalScore'];
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
    
    $params = array_merge([$event, $parti, $jid, $total], $criteria_values);
    $types = "iiid" . str_repeat("d", count($criteria_values));
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo '<script>alert("Recorded!");</script>';
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
    <link rel="stylesheet" href="/assets/css/SCevents.css">
    <link rel="icon" href="/assets/icons/logo.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="/assets/icons/logoo.png" alt="Logo">
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
            <i class="fas fa-sign-out-alt" onclick="window.location.href = 'landing-page.html';"></i>
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
                    addCriteriaFormEventListener();
                }
            };
            xhttp.open("POST", "get_eventFrom.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("evid=" + eventId);
        }

        function calculateTotal() {
            var criteriaInputs = document.getElementsByClassName('criteriaInput');
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
                    alert("You have exceeded the maximum score.");
                    return;
                }

                totalScore += score;
            }

            document.getElementById('totalScore').value = totalScore;
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

    <?php
        if(isset($_GET['event'])) {
            $evId = $_GET['event'];
            echo "<script>loadEventCriteria($evId)</script>";
        }
    ?>
</body>

</html>
