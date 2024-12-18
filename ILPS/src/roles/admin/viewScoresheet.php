<?php

require_once '../../../config/db.php'; // Database connection

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

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">
</head>

<body>

    <div class="header-container">
        <div class="heading1">
            <img src="../../../public/assets/icons/useologo.png" alt="USeP logo" width="80"><br>
            <b>University of Southeastern Philippines</b><br>
            <i>Office of the Student Affairs and Services<br>Tagum-Mabini Campus</i>
        </div>
        <div class="eventname-container">
            <h1><?php echo $evname ?></h1>
        </div>
    </div>


    <div class="tally-container" id="TallyTable">
        <table id="tallyTable">

            <tr id="tabletr"> <!-- TABLE COLUMN HEADER -->
                <td id="headCol">Contest No.</td>
                <?php
                $get = "SELECT DISTINCT personnelId FROM vw_subresult WHERE eventId = ?";

                $stmt = $conn->prepare($get);
                $stmt->bind_param("i", $evId);
                $stmt->execute();
                $retval = $stmt->get_result();

                $judges = array(); // Store personnelId

                $judgeCount = 0;
                $judgeColumns = [];

                if ($retval->num_rows > 0) {
                    while ($row = $retval->fetch_assoc()) {
                        $judgeCount++;
                        $judges[] = $row;

                        // Store Judge IDs for Conditional Statement Later
                        $judgeColumns[] = 'MAX(CASE WHEN personnelId = ' . $row['personnelId'] . ' 
                                            THEN total_score END) AS judge_' . $row['personnelId'];
                ?>
                        <td id="headCol">JUDGE <?php echo $judgeCount; ?></td>
                <?php
                    }
                }
                $retval->free();
                $stmt->close();

                // Combine judge columns into one SQL string
                $judgeColumnsSql = implode(', ', $judgeColumns);
                ?>

                <td id="headCol">Total Score</td>
                <td id="headCol">Rank</td>
            </tr>

            <?php // Data Row

            // The final SQL query
            $getRowData = "SELECT vp.contNo, $judgeColumnsSql,
                                    SUM(vs.total_score) AS Total,
                                    RANK() OVER (ORDER BY SUM(vs.total_score) DESC) AS rank
                            FROM vw_subresult vs
                            INNER JOIN vw_eventParti vp ON vs.contestantId = vp.contId
                            WHERE vs.eventId = ?
                            GROUP BY vp.contId";

            $stmt = $conn->prepare($getRowData);
            $stmt->bind_param("i", $evId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Table Data
                while ($row = $result->fetch_assoc()) {
                    echo '<tr style="text-align: left;" id="tabletr">';
                    echo '<td>' . $row['contNo'] . '</td>';

                    foreach ($judges as $judge) {
                        echo '<td>' . $row['judge_' . $judge['personnelId']] . '</td>';
                    }
                    echo '<td>' . $row['Total'] . '</td>';
                    echo '<td>' . $row['rank'] . '</td>';

                    echo '</tr>';
                }
            }
            ?>

        </table>
    </div>

    <div class="facilitators-container">
        <?php
        $sql = "CALL sp_getJudges(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $evId);
        $stmt->execute();
        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            while ($row = $retval->fetch_assoc()) {
                $judge = $row['perName'];
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