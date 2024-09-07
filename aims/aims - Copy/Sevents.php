<?php
include 'encryption.php';

$conn = include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$id = $_SESSION['faciId'];


$evId = isset($_GET['event']) ? $_GET['event'] : '';
$evname = isset($_GET['name']) ? $_GET['name'] : '';
$contestant = isset($_GET['contestant']) ? $_GET['contestant'] : '';


if(isset($_POST['scoreCon'])) {
    $eventId = $_POST['evId'];
    $teamId = $_POST['cont'];
    $total = $_POST['score'];

    $sql = "CALL sp_insertResultComt(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $eventId, $teamId, $id, $total);

    if ($stmt->execute()) {
        echo "<script>alert('Score Recorded.')</script>";
    } else {
        echo "Error: ".mysqli_error($conn);
    }
    $stmt->close();
}

if(isset($_POST['changeScore'])) {
    $eventId = $_POST['evId'];
    $teamId = $_POST['cont'];
    $total = $_POST['score'];
 
    $sql = "CALL sp_updateScore(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("diii", $total, $id, $eventId, $teamId);

    if ($stmt->execute()) {
        echo "<script>alert('Score Updated.')</script>";
    } else {
        echo "Error: ".mysqli_error($conn);
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="/assets/css/Sevents.css">
    <link rel="icon" href="/assets/icons/logo-1.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
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
            <i class="fas fa-sign-out-alt" onclick="window.location.href = 'index.html';"></i>
        </div>
    </div>


    <div class="sub-head" style="margin-top: 8%;">
        <button id="backbtn-faci" onclick="window.location.href='committee.php?id=<?php echo $id; ?>'">
            <img src="/assets/icons/back.png" alt="back arrow button" width="20" style="margin-right: 5px;">  
            Back
        </button>
        <h1 style="text-align: center;"><?php echo $evname; ?></h1>
        <div class="scoring-container">
            <table>
                <tr>
                    <th colspan="3">Event Scoring Guide:</th>
                </tr>
            <?php
                $sql = "CALL sp_getEvent(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $evId);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                    $row = $retval->fetch_assoc();
                    $catg = $row['eventCategory'];
                }
                $retval->free();
                $stmt->close();

                $sql = "CALL sp_getScoringDets(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $catg);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                    while($row = $retval->fetch_assoc()) {
                        $num = $row['rankNo'];
                        $rkname = $row['rank'];
                        $rkpts = $row['points'];
                        ?>
                        <tr>
                            <td>Rank: <?php echo $num?></td>
                            <td>- <?php echo $rkname?></td>
                            <td style="padding-left: 15px;">Points: <?php echo $rkpts?></td>
                        </tr>
                        <?php
                    }
                }
                $retval->free();
                $stmt->close();
            ?>
            </table>
        </div>
    </div>


    <div class="link-container">
    <?php
        $query = "CALL sp_getEventContestant(?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $evId);
        $stmt->execute();
        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            while($row = $retval->fetch_assoc()) {
                $conid = $row['contId'];
                ?>
    <a href="Sevents.php?event=<?php echo $evId ?>&name=<?php echo $evname ?>&contestant=<?php echo $conid ?>"><?php echo $row['team']; ?></a>
                <?php
            }
        }
        $retval->free();
        $stmt->close();
    ?>
    <a href="Sevents.php?event=<?php echo $evId ?>&name=<?php echo $evname ?>&contestant=0">View Tally</a>
    </div>



    

    <form id="scoreForm" method="post" enctype="multipart/form-data" style="display: none;">
        <input type="number" name="evId" id="evId" value="<?php echo ($evId); ?>" hidden>
        <input type="number" name="cont" id="cont" value="<?php echo ($contestant); ?>" hidden>

        <?php
        // Prepare and execute the first query
        $sql = "CALL sp_getTeams(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $contestant);
        $stmt->execute();
        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            $row = $retval->fetch_assoc();
            $conName = ($row['teamName']);
        } else {
            $conName = "Unknown Team"; // Fallback if no results
        }
        $retval->free();
        $stmt->close();
        ?>
        <b>Enter score for <?php echo $conName; ?></b><br><br>

        <?php
        // Prepare and execute the second query
        $sql = "CALL sp_getScore(?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $contestant, $evId);
        $stmt->execute();
        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            $row = $retval->fetch_assoc();
            $score = ($row['total_score']);

            $retval->free();
            $stmt->close();

            // Prepare and execute the third query
            $get = "SELECT * FROM vw_eventScore WHERE eventCategory = 
                (SELECT eventCategory FROM vw_events WHERE eventID = ?);";
            $prep = $conn->prepare($get);
            $prep->bind_param("i", $evId);
            $prep->execute();

            $retrieve = $prep->get_result();

            if ($retrieve->num_rows > 0) {
                ?>
                <select name="score">
                <?php
                    while ($row = $retrieve->fetch_assoc()) {
                        $pts = htmlspecialchars($row['points']);
                        $rank = htmlspecialchars($row['rank']);

                        $selected = ($pts == $score) ? 'selected' : '';
                ?>
                    <option value="<?php echo $pts; ?>" <?php echo $selected; ?>>
                        <?php echo "$rank - $pts pts."; ?>
                    </option>
                <?php
                    }
                ?>
                </select>
                <div class="submit-btn">
                    <button type="submit" name="changeScore">Change</button>
                </div>
                <?php
            }
        } else {
            $retval->free();
            $stmt->close();

            // Prepare and execute the third query
            $get = "SELECT * FROM vw_eventScore WHERE eventCategory = 
                (SELECT eventCategory FROM vw_events WHERE eventID = ?);";
            $prep = $conn->prepare($get);
            $prep->bind_param("i", $evId);
            $prep->execute();

            $retrieve = $prep->get_result();

            if ($retrieve->num_rows > 0) {
            ?>
            <select name="score">
            <?php
                while ($row = $retrieve->fetch_assoc()) {
                    $pts = htmlspecialchars($row['points']);
                    $rank = htmlspecialchars($row['rank']);
            ?>
                <option value="<?php echo $pts; ?>"><?php echo "$rank - $pts pts."; ?></option>
            <?php
                }
            ?>
            </select>
            <div class="submit-btn">
                <button type="submit" name="scoreCon">Submit</button>
            </div>
            <?php
            } else {
                echo "No scores available.";
            }
            $retrieve->free();
            $prep->close();
        }
        ?>
    </form>


    <div class="tally-container" id="TallyTable" style="display: none;">
        <table id="tallyTable">
            <tr style="text-align: center;" id="tabletr">
                <td id="headCol">Contest Name</td>
                <td id="headCol">Score</td>
                <td id="headCol">Rank</td>
            </tr>
            
                <?php
                    $get = "CALL sp_getScoreSport(?)";
                    $stmt = $conn->prepare($get);
                    $stmt->bind_param("i", $evId);
                    $stmt->execute();
                    $retval = $stmt->get_result();

                    if ($retval->num_rows > 0) {
                        $rank = 1;
                        while($row = $retval->fetch_assoc()) {
                            $tmname = $row['name'];
                            $tscore = $row['total_score'];
                            ?>
                            <tr style="text-align: left;" id="tabletr">
                                <th><?php echo $tmname; ?></th>
                                <td id="scoreCol"><?php echo $tscore; ?></td>
                                <th><?php echo $rank++; ?></th>
                            </tr>
                            <?php
                        }
                    }
                    $retval->free();
                    $stmt->close();
                ?>
            <tr id="tabletr" style="text-align: center; color:crimson">
                <td colspan="3"><b style="background-color: lightcoral;">In case of tie, please break the tie by changing the scores.</b></td>
            </tr>
        </table>
    </div>


    <script>
        function displayForm() {
            var form = document.getElementById('scoreForm');
            console.log("DisplayForm function called");
            form.style.display = 'block';
        }

        function displayTally() {
            var form = document.getElementById('TallyTable');
            form.style.display = 'block';
        }
    </script>
    
<?php
echo "<script>console.log('Contestant value: " . $contestant . "');</script>";
if($contestant != 0 && $contestant != '') {
    echo "<script>displayForm();</script>";
}
if ($contestant == 0 && $contestant != '') {
    echo "<script>displayTally();</script>";
}
?>

</body>
</html>
