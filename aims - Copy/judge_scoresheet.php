<?php
    $conn = include 'db.php';
  
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $evId = $_GET['event'];
    $evname = $_GET['evname'];
    $perId = $_GET['personnel'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="assets/css/tally.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/logo-1.png">
</head>
<body>
    
    <div class="header-container">
        <div class="heading1">
            <img src="assets/icons/logo-1.png" alt="ILPS logo" width="80"><br>
            <b>Intramural Leaderboard and Points System</b><br>
            123 Street Barangay Apokon, Tagum City, Davao Del Norte <br>
            (555) 123-4567 . <span style="color: #4B8946;">Dreamy Inc</span> . john.doe@example.com
        </div>
        <div class="eventname-container">
            <h1><?php echo $evname ?></h1>
        </div>
    </div>




    <div class="tally-container" id="TallyTable">
        <table id="tallyTable">
            <tr id="tabletr">
                <td id="headCol">Contest Name</td>
                <?php
                    $get = "CALL sp_getCriteria(?)";
                    $stmt = $conn->prepare($get);
                    $stmt->bind_param("i", $evId);
                    $stmt->execute();
                    $retval = $stmt->get_result();

                    $criCount = 0;
                    if ($retval->num_rows > 0) {
                        while($row = $retval->fetch_assoc()) {
                            $criCount++;
                            $cri = $row['criteria'];
                            ?>
                            <td id="headCol"><?php echo $cri; ?></td>
                            <?php
                        }
                    }
                    $retval->free();
                    $stmt->close();
                ?>
                <td id="headCol">Total Score</td>
            </tr>
            
                <?php
                    $get = "CALL sp_getScoreJudge(?,?,?);";
                    $stmt = $conn->prepare($get);
                    $stmt->bind_param("iii", $evId, $evId, $perId);
                    $stmt->execute();
                    $retval = $stmt->get_result();

                    if ($retval->num_rows > 0) {
                        $rank = 1;
                        while($row = $retval->fetch_assoc()) {
                            $tmname = $row['teamname'];
                            $tscore = $row['total_score'];
                            ?>
                            <tr style="text-align: left;" id="tabletr">
                                <td><?php echo $tmname; ?></td>
                                <?php
                                    for($x = 1; $x <= $criCount; $x++) {
                                        ?>
                                        <td id="scoreCol"><?php echo $row['criteria'.$x]; ?></td>
                                        <?php
                                    }
                                ?>
                                <td><?php echo $tscore; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    $retval->free();
                    $stmt->close();
                ?>
        </table>
    </div>

    <div class="facilitators-container">
        <?php
            $sql = "CALL sp_getJudge(?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $perId, $evId);
            $stmt->execute();
            $retval = $stmt->get_result();

            if ($retval->num_rows > 0) {
                while($row = $retval->fetch_assoc()) {
                    $judge = $row['firstName'];
                    ?>
                    <div class="col">
                        <b><?php echo $judge ?></b><br>
                        Event Judge
                    </div>
                    <?php
                }
            }
            $retval->free();
            $stmt->close();
        ?>
    </div>

</body>
</html>