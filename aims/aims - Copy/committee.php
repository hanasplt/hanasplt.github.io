<?php
    session_start();

    $conn = include 'db.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $_SESSION['faciId'] = $id;
    }

    $sql = "CALL sp_getAnAcc(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        $row = $retval->fetch_assoc();
        $status = $row['log_status'];

        $retval->free();
        $stmt->close();
    }

    if($status != 'finish') {
        header('Location: change_passfaci.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ILPS</title>
    <link rel="stylesheet" href="assets/css/faci.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/logo-1.png">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>COMMITTEE</i></p>
        </div>
        <div class="links">
            <p onclick="window.location.href = 'admin.html';" hidden>Home</p>
            <p onclick="window.location.href = 'accounts.html';" hidden>Accounts</p>
            <p onclick="window.location.href = 'create-team.html';" hidden>Teams</p>
            <p onclick="window.location.href = 'EventTeam.html';" hidden>Events</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" style="cursor: pointer;" onclick="window.location.href = 'index.html';"></i>
        </div>
    </div>
    
    <div class="eventsDropdown">
        <h4><i>Events: </i></h4>
        <ul>
            <?php
                $sql = $sql = "CALL sp_getAComt(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $retval = $stmt->get_result();
        
                if ($retval->num_rows > 0) {
                    while($row = $retval->fetch_assoc()) {
                        ?>
                        <li>
                            <a href="Sevents.php?event=<?php echo $row['eventId']; ?>"&name=<?php echo $row['eventName']; ?>>
                                <?php echo $row['eventName']; ?>
                            </a>
                        </li>
                        <?php
                    }
                    $retval->free();
                    $stmt->close();
                } else {
                    ?>
                    <li>
                        No event/s.
                    </li>
                    <?php
                }
            ?>
        </ul>
    </div>

    <script>
        function showSelectedForm() {
            var dropdownMenu = document.getElementById("dropdownMenuEvents");
            var selectedEvent = dropdownMenu.value;
            var evName = dropdownMenu.options[dropdownMenu.selectedIndex].text;

            if(selectedEvent != "") {
                window.location.href = "Sevents.php?event="+selectedEvent+"&name="+evName;
            }
        }
    </script>

    <?php $conn->close(); ?>
</body>

</html>
