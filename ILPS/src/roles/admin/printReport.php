<?php
require_once '../../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
        .header {
            top: 0px;
            left: 0px;
            right: 0px;
            height: 150px;
            text-align: center;
        }
        .content {
            page-break-after: always;
        }
        .header-image {
            width: 100px;
            margin: 0 auto;
        }
        .usep-name {
            font-family: Old English Text MT;
            font-size: 20px;
        }
        .page-number:before {
            content: counter(page);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 15px;
            text-align: left;
        }
        .vision-line {
            border-top: 1px solid black;
        }
        main {
            margin-top: 80px;
        }
        img {
            width: 140px;
        }
    </style>
</head>
<body>
    <?php
    $sql = "CALL sp_scoreReport()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $retval = $stmt->get_result();
    ?>
    <div class="header">
        <img src="../../../public/assets/icons/useologo.png" class="header-image" alt="usep logo"><br>
        <span class="usep-name">University of Southeastern Philippines</span><br>
        <i>Office of the Student Affairs and Services<br>Tagum-Mabini Campus</i><br>
        <h2 style="margin: 15px 0;">Score Report</h2>
    </div>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Team Name</th>
                    <th>Points</th>
                    <th>Action</th>
                    <th>Action At</th>
                </tr>
            </thead>
            <tbody>
        <?php
        if ($retval->num_rows > 0) {
            while ($row = $retval->fetch_assoc()) {
                $formatted_date = "<script>
                    function formatDate(dateString) {
                        const date = new Date(dateString);
                        const options = { 
                            month: 'long', 
                            day: 'numeric', 
                            year: 'numeric', 
                            hour: '2-digit', 
                            minute: '2-digit', 
                            hour12: true 
                        };
                        return date.toLocaleString('en-US', options);
                    }
                    document.write(formatDate('" . $row['action_at'] . "'));
                </script>";
                ?>
                <tr>
                    <td><?php echo $row['eventName']; ?></td>
                    <td><?php echo $row['teamName']; ?></td>
                    <td><?php echo $row['total_score']; ?></td>
                    <td><?php echo $row['action_made']; ?></td>
                    <td><?php echo $formatted_date; ?></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="5">No reports exist.</td></tr>';
        }
        ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php
}
?>