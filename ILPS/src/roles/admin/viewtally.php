<?php

    $conn = require_once '../../../config/db.php'; // Database connection

    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }

    $evId = $_GET['event'];
    $evname = $_GET['evname'];
    $purpose = isset($_GET['purpose']) ? $_GET['purpose'] : '';

    if ($purpose !== '') {
        echo "<script>window.print();</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="../admin/css/tally.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">
</head>
<body>
    
    <div class="header-container">
        <div class="heading1">
            <img src="../../../public/assets/icons/logo-1.png" alt="ILPS logo" width="80"><br>
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
                                <td><?php echo $tmname; ?></td>
                                <td id="scoreCol"><?php echo $tscore; ?></td>
                                <td><?php echo $rank; ?></td>
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
            $sql = "CALL sp_getEventComt(?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $evId);
            $stmt->execute();
            $retval = $stmt->get_result();

            if ($retval->num_rows > 0) {
                while($row = $retval->fetch_assoc()) {
                    $faci = $row['firstName'];
                    ?>
                    <div class="col">
                        <b><?php echo $faci ?></b><br>
                        Event Committee
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