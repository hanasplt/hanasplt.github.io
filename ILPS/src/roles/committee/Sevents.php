<?php

require_once '../../../config/sessionConfig.php'; // Session Cookie
$conn = require_once '../../../config/db.php'; // Database connection
require_once '../admin/verifyLoginSession.php'; // Logged in or not

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['userId'];


$evId = isset($_GET['event']) ? $_GET['event'] : '';
$evname = isset($_GET['name']) ? $_GET['name'] : '';
$contestant = isset($_GET['contestant']) ? $_GET['contestant'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../committee/css/Sevents.css">
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>
<body>

    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>COMMMITTEE</i></p>
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


    <div class="sub-head" style="margin-top: 8%;">
        <button id="backbtn-faci" onclick="window.location.href='committee.php?id=<?php echo $id; ?>'">
            <img src="../../../public/assets/icons/back.png" alt="back arrow button" width="20" style="margin-right: 5px;">  
            Back
        </button>
        <h1 style="text-align: center;"><?php echo $evname; ?></h1>
        <?php
            if (isset($_SESSION['error'])) { // For displaying error
                echo '
                <div class="msg" id="msg-container">
                    <div class="msg-content">
                        <span style="display: flex; align-items: center; justify-content: space-around;">
                            <p id="error-msg">' . $_SESSION['error'] . '</p>
                            <button type="button" id="x-btn">X</button>
                        </span>
                    </div>
                </div>
                ';
                unset($_SESSION['error']);
            }
        ?>
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



    

    <form id="scoreForm" method="post" action="../committee/SeventsProcess.php" style="display: none;">
        <input type="number" name="evId" id="evId" value="<?php echo ($evId); ?>" hidden>
        <input type="number" name="cont" id="cont" value="<?php echo ($contestant); ?>" hidden>
        <input type="number" name="id" id="id" value="<?php echo ($id); ?>" hidden>
        <input type="text" name="evname" id="evname" value="<?php echo ($evname); ?>" hidden>

        <?php
        // Retrieve team info.
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

        // Ensure no more results are pending for this query
        $conn->next_result();
        ?>
        <input type="text" name="conname" id="conname" value="<?php echo ($conName); ?>" hidden>
        <b>Enter score for <?php echo $conName; ?></b><br><br>

        <?php
        // Retrieve score of the contestant
        $sql = "CALL sp_getScore(?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $contestant, $evId);
        $stmt->execute();
        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            // Select the dropdown points that's equivalent to $score
            $row = $retval->fetch_assoc();
            $score = ($row['total_score']);

            $retval->free();
            $stmt->close();

            // Ensure no more results are pending for this query
            $conn->next_result();

            // Retrieve Score Points for dropdown
            $get = "CALL sp_getScorePts(?)";
            $prep = $conn->prepare($get);
            $prep->bind_param("i", $evId);
            $prep->execute();

            $retrieve = $prep->get_result();

            if ($retrieve->num_rows > 0) {
                ?>
                <select name="score" id="score">
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
                    <button type="submit" class="changeScore-btn" name="changeScore">Change</button>
                </div>
                <?php
            }
            $retrieve->free();
            $prep->close();

            // Ensure no more results are pending for this query
            $conn->next_result();

        } else { // Just display the score points
            $retval->free();
            $stmt->close();

            // Ensure no more results are pending for this query
            $conn->next_result();

            // Retrieve Score Points for dropdown
            $get = "CALL sp_getScorePts(?);";
            $prep = $conn->prepare($get);
            $prep->bind_param("i", $evId);
            $prep->execute();

            $retrieve = $prep->get_result();

            if ($retrieve->num_rows > 0) {
            ?>
            <select name="score" id="score">
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
                <button type="submit" class="scoreContestant" name="scoreCon">Submit</button>
            </div>
            <?php
            } else { // No score added yet by the admin
                echo "No scores available.";
            }
            $retrieve->free();
            $prep->close();

            // Ensure no more results are pending for this query
            $conn->next_result();
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
                        while($row = $retval->fetch_assoc()) {
                            $tmname = $row['name'];
                            $tscore = $row['total_score'];
                            $rank = $row['rank'];
                            ?>
                            <tr style="text-align: left;" id="tabletr">
                                <th><?php echo $tmname; ?></th>
                                <td id="scoreCol"><?php echo $tscore; ?></td>
                                <th><?php echo $rank; ?></th>
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

    <script src="../committee/js/Sevents.js"></script>
    
<?php
if($contestant != 0 && $contestant != '') {
    echo "<script>displayForm();</script>";
}
if ($contestant == 0 && $contestant != '') {
    echo "<script>displayTally();</script>";
}
?>

</body>
</html>
