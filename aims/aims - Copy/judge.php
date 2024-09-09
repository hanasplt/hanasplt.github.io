<?php
    session_start();

    $servername = "localhost"; 
    $username = "root"; 
    $password = "";
    $dbname = "ilps";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $_SESSION['judgeId'] = $id;
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
        header('Location: change_pass.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="stylesheet" href="/assets/css/judge.css">
    <link rel="icon" href="/assets/icons/logo-1.png">

    <!-- icons -->
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
            <p onclick="window.location.href = 'admin.php';" hidden>Home</p>
            <p onclick="window.location.href = 'accounts.php';" hidden>Accounts</p>
            <p onclick="window.location.href = 'teams.php';" hidden>Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="active-link" hidden>Events</p>
        </div>

        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>

    <div class="eventsDropdown">
        <h4><i>Events: </i></h4>
        <select class="dropdownEvents" id="dropdownMenuEvents" onchange="showSelectedForm()">
            <option value="" selected disabled>Select an event</option>
            <?php
                $sql = "CALL sp_getAJudge(?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $retval = $stmt->get_result();
        
                if ($retval->num_rows > 0) {
                    while($row = $retval->fetch_assoc()) {
                        ?>
                        echo "<option value="<?php echo $row['eventId']; ?>"><?php echo $row['eventName']; ?></option>";
                        <?php
                    }
                    $retval->free();
                    $stmt->close();
                } else {
                    echo "error: ".mysqli_error($conn);
                }
            ?>
        </select>
    </div>

    <script>
        function showSelectedForm() {
            var selectedEvent = document.getElementById("dropdownMenuEvents").value;
            console.log('selected event: ', selectedEvent);

            if(selectedEvent != "") {
                window.location.href = "SCevents.php?event="+selectedEvent;
            }
        }
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
</body>

</html>
