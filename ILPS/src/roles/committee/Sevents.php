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

    
    <!-- NEW INTERFACE -->
    <div class="recordTable">
        <table style="margin: auto">
            <tr>
                <td>TIME</td>
                <td>EVENT</td>
                <td>GAME NO.</td>
                <td>TEAM A ( ERUDITE )</td>
                <td>POINTS</td>
                <td>TEAM B ( AMITY )</td>
                <td>POINTS</td>
                <td>ACTION</td>
            </tr>
            <tr>
                <td>10/08/2024 1:00 PM</td>
                <td>Basketball</td>
                <td>1</td>
                <td>
                    <select id="teamA" class="non-editable" onchange="syncTeams('teamA')" disabled>
                        <option value=""></option>
                        <option value="Winner">Winner</option>
                        <option value="Loser">Loser</option>
                    </select>
                </td>
                <td>
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
                            <select name="score" id="score" class="non-editable" onchange="updateScore(<?php echo $contestant; ?>, <?php echo $evId; ?>)" disabled>
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
                            <div id="response"></div> <!-- For showing feedback message -->
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
                            <select name="score" id="score" class="non-editable" onchange="updateScore(<?php echo $contestant; ?>, <?php echo $evId; ?>)" disabled>
                                <option value="0">0</option>
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
                            <div id="response"></div> <!-- For showing feedback message -->
                    <?php
                        } else {
                            echo "No scores available.";
                        }
                        $retrieve->free();
                        $prep->close();

                        // Ensure no more results are pending for this query
                        $conn->next_result();
                    }
                    ?>

                </td>
                <td>
                    <select id="teamB" class="non-editable" onchange="syncTeams('teamB')" disabled>
                        <option value=""></option>
                        <option value="Winner">Winner</option>
                        <option value="Loser">Loser</option>
                    </select>
                </td>
                <td>
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
                            <select name="score" id="score" class="non-editable" onchange="updateScore(<?php echo $contestant; ?>, <?php echo $evId; ?>)" disabled>
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
                            <div id="response"></div> <!-- For showing feedback message -->
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
                            <select name="score" id="score" class="non-editable" onchange="updateScore(<?php echo $contestant; ?>, <?php echo $evId; ?>)" disabled>
                                <option value="0">0</option>
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
                            <div id="response"></div> <!-- For showing feedback message -->
                    <?php
                        } else {
                            echo "No scores available.";
                        }
                        $retrieve->free();
                        $prep->close();

                        // Ensure no more results are pending for this query
                        $conn->next_result();
                    }
                    ?>
                </td>
                <td>
                    <button class="edit-btn" data-event-id="<?php echo $event['id']; ?>">Edit</button>
                    <button class="save-btn" style="display: none;" data-event-id="<?php echo $event['id']; ?>">Save</button>
                </td>
            </tr>
        </table>
    </div>
    

    <!-- SYNC TEAM RESULT -->
    <script>
        function syncTeams(changedTeam) {
            const teamASelect = document.getElementById('teamA');
            const teamBSelect = document.getElementById('teamB');


            // Check which dropdown was changed
            if (changedTeam === 'teamA') {
                // If team A is set to 'Winner', automatically set team B to 'Loser'
                // and if set to 'Loser', set team B to 'Winner'
                if (teamASelect.value === 'Winner') {
                    teamBSelect.value = 'Loser';
                    teamBSelect.style.color = 'red';
                    teamASelect.style.color = 'green';
                } else if (teamASelect.value === 'Loser') {
                    teamBSelect.value = 'Winner';
                    teamBSelect.style.color = 'green';
                    teamASelect.style.color = 'red';
                }
            } else if (changedTeam === 'teamB') {
                // Similarly for team B
                if (teamBSelect.value === 'Winner') {
                    teamASelect.value = 'Loser';
                    teamASelect.style.color = 'red';
                    teamBSelect.style.color = 'green';
                } else if (teamBSelect.value === 'Loser') {
                    teamASelect.value = 'Winner';
                    teamASelect.style.color = 'green';
                    teamBSelect.style.color = 'red';
                }
            }
        }
    </script>

    <script>
        // Function to enable editing and show the Save button
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');

                // Enable the dropdowns in the current row
                row.querySelectorAll('select').forEach(select => {
                    select.disabled = false;
                });
                // Hide the Edit button and show the Save button again
                row.querySelector('.edit-btn').style.display = 'none';
                // Hide the Delete button and show the Save button
                row.querySelector('.save-btn').style.display = 'inline-block';

                // Optionally, you can disable the Edit button after clicking
                this.disabled = true;
            });
        });

        // Function to save changes and revert the Save button to Delete
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');

                // Disable the dropdowns again after saving
                row.querySelectorAll('select').forEach(select => {
                    select.disabled = true;
                });

                // Hide the Save button and show the Edit button again
                row.querySelector('.save-btn').style.display = 'none';

                // Optionally, re-enable the Edit button
                row.querySelector('.edit-btn').disabled = false;

                // Hide the Save button and show the Edit button again
                row.querySelector('.edit-btn').style.display = 'block';

                // You can also trigger the actual saving logic (e.g., AJAX call) here
                // For now, just display a message
                console.log("Changes saved!");
            });
        });
    </script>
</body>

</html>