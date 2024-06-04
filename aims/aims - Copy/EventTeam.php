<?php
  include 'encryption.php';

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "ilps";

  $conn = new mysqli($servername, $username, $password, $database);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  //add event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $eventName = $_POST['eventName'];
    $eventType = $_POST['eventType'];
    $eventCategory = $_POST['eventCategory'];

    $sql = "CALL sp_insertEvent(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $eventId, $eventName, $eventType, $eventCategory);
    if ($stmt->execute()) {
        echo '<script>alert("New event added successfully!");</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
  }

  //add event contestant
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addContestant') {
    $team = $_POST['contestantId'];
    $name = $_POST['contestantName'];
    $event = $_POST['conEvId'];

    $sql = "CALL sp_getContestant(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $team, $event);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo '<script>alert("Contestant already exists!");</script>';
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertEventContestant(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $team, $name, $event);
    
        if ($stmt->execute()) {
            echo '<script>alert("Contestant added successfully!");</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $stmt->close();
  }

  //add event faci
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addFaci') {
    $faciid = $_POST['faciId'];
    $evid = $_POST['faciEvId'];
    $event = $_POST['faciEVName'];
    $faciname = $_POST['faciName'];

    $sql = "CALL sp_getFaci(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $faciid, $evid);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo '<script>alert("Facilitator already exists!");</script>';
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertEventFaci(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $faciid, $evid, $event, $faciname);
    
        if ($stmt->execute()) {
            echo '<script>alert("Facilitator added successfully!");</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $stmt->close();
  }

  //add event judge
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addJudge') {
    $judgeid = $_POST['judgeId'];
    $evid = $_POST['judgeEvId'];
    $event = $_POST['judgeEVName'];
    $judgename = $_POST['judgeName'];

    $sql = "CALL sp_getJudge(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $judgeid, $evid);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo '<script>alert("Judge already exists!");</script>';
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertEventJudge(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $judgeid, $evid, $event, $judgename);
    
        if ($stmt->execute()) {
            echo '<script>alert("Judge added successfully!");</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $stmt->close();
  }

  //add event criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addCriteria') {
    $event = $_POST['criEVId'];
    $criteria = $_POST['criteria'];
    $pts = $_POST['criPts'];

    $sql = "CALL sp_insertCriteria(?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $event, $criteria, $pts);

    if ($stmt->execute()) {
        echo '<script>alert("Criteria added successfully!");</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
  }

  //edit criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editCriteria') {
    $criid = $_POST['editcriId'];
    $cri = $_POST['editcriteria'];
    $pts = $_POST['editcriPts'];
    
    $sql = "CALL sp_editCriteria(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $criid, $cri, $pts);

    if ($stmt->execute()) {
        echo '<script>alert("Criteria updated successfully!");</script>';
    }
    $stmt->close();
  }

  //add scoring (pending)
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addScoring') {
    $ranknum = $_POST['rankNo'];
    $name = $_POST['rankName'];
    $category = $_POST['scoringCategory'];
    $pts = $_POST['scorePts'];

    $sql = "CALL sp_getScoringChk(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $ranknum, $category);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo '<script>alert("Scoring already exists!");</script>';
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertScoring(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $ranknum, $name, $category, $pts);
    
        if ($stmt->execute()) {
            echo '<script>alert("Scoring added successfully!");</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $stmt->close();
  }

  //edit event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editEvent') {
    $eventid = $_POST['editeventId'];
    $eventtype = $_POST['editeventType'];
    $eventname = $_POST['editeventName'];
    $eventcat = $_POST['editeventCategory'];
    
    $sql = "CALL sp_editEvent(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $eventid, $eventtype, $eventname, $eventcat);

    if ($stmt->execute()) {
        echo '<script>alert("Event updated successfully!");</script>';
    }
    $stmt->close();
  }

  //deletes an event
  if (isset($_GET['eventid'])) {
    $eventID = $_GET['eventid'];
    
    $sql = "CALL sp_delEvent(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);

    if ($stmt->execute()) {
      echo "<script>alert('Event deleted successfully!'); window.location.href='EventTeam.php';</script>";
    }
    $stmt->close();
  }

  //deletes a contestant
  if (isset($_GET['contid'])) {
    $id = $_GET['contid'];
    
    $sql = "CALL sp_delEventContestant(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      echo "<script>alert('Contestant deleted successfully!'); window.location.href='EventTeam.php';</script>";
    }
    $stmt->close();
  }

  //deletes a faci
  if (isset($_GET['faciid'])) {
    $faciID = $_GET['faciid'];
    
    $sql = "CALL sp_delEventFaci(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $faciID);

    if ($stmt->execute()) {
      echo "<script>alert('Facilitator deleted successfully!'); window.location.href='EventTeam.php';</script>";
    }
    $stmt->close();
  }

  //deletes a judge
  if (isset($_GET['judgeid'])) {
    $judgeID = $_GET['judgeid'];
    
    $sql = "CALL sp_delEventJudge(?)";  
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $judgeID);

    if ($stmt->execute()) {
      echo "<script>alert('Judge deleted successfully!'); window.location.href='EventTeam.php';</script>";
    }
    $stmt->close();
  }

  //deletes a criteria
  if (isset($_GET['criid'])) {
    $criID = $_GET['criid'];
    
    $sql = "CALL sp_delCriteria(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $criID);

    if ($stmt->execute()) {
      echo "<script>alert('Criteria deleted successfully!'); window.location.href='EventTeam.php';</script>";
    }
    $stmt->close();
  }

  //deletes a scoring
  if (isset($_GET['rank'])) {
    $rank = $_GET['rank'];
    
    $sql = "CALL sp_delScoring(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rank);

    if ($stmt->execute()) {
      echo "<script>alert('Scoring deleted successfully!'); window.location.href='EventTeam.php';</script>";
    }
    $stmt->close();
  }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/EventTeam.css">

    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="/assets/icons/logo-1.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>ADMINISTRATOR</i></p>
        </div>

        <div class="links">
            <p onclick="window.location.href = 'admin.php';">Home</p>
            <p onclick="window.location.href = 'accounts.php';">Accounts</p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="active-link">Events</p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" style="cursor: pointer;" onclick="window.location.href = 'landing-page.html';"></i>
        </div>
    </div>

    <div class="dash">
        <div class="create" id="openModal">
            <div class="new-account" id="openPopup">
                <div class="plus-icon">
                    <i class="fas fa-plus"></i>
                </div>

                <div class="new-account-info">
                    <p id="create">Create an Event</p>
                    <p id="add">Add a new event</p>
                </div>
            </div>
        </div>
    </div>

    <div class="accounts">
        <div class="accounts-title">
            <p id="event">Event Type</p>
        </div>

        <div>
            <div class="account" onclick="toggleSubEvents('sportsSubEvents')">
                <div class="left-deets">
                    <div class="acc-img">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="acc-deets" id="theatreArts">
                        <p id="name">Sports</p>
                    </div>
                </div>
            </div>

            <div class="accounts" id="sportsSubEvents" style="display: none;">
                <div class="accounts-title">
                    <p id="event">Events</p>
                </div>

                <?php
                //retrieve event (sports)
                $sql = "CALL sp_getEventFrom(?)";
                $sport = "Sports";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $sport);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $ID = $row['eventID'];
                        $evName = str_replace(' ', '', $row['eventName']);
                        $eventName = $row['eventName'];
                        $eventType = $row['eventType'];
                        $eventCategory = $row['eventCategory'];
                        ?>
                        <div class="sub-account" id="<?php echo $eventName; ?>" data-id="<?php echo $ID; ?>" data-name="<?php echo $eventName; ?>" data-type="<?php echo $eventType; ?>" data-category="<?php echo $eventCategory; ?>">
                            <div class="left-deets" onclick="toggleTable('<?php echo $evName; ?>Table')">
                                <div class="acc-img">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="acc-deets">
                                    <p id="name"><?php echo $eventName; ?></p>
                                </div>
                            </div>
                            <div class="right-deets">
                                <div class="acc-buttons">
                                    <div class="subtrash-icon" onclick="deleteThis(<?php echo $ID; ?>)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </div>

                                    <div class="subedit-icon" onclick="openEditEvModal(this)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container" id="<?php echo $evName; ?>Table" style="display: none;">
                            <div class="buttons">
                                <button class="contestant loadContestantsBtn" onclick="toggleSubTable('<?php echo $evName; ?>ContestantTable')" data-event="<?php echo $ID; ?>" data-type="<?php echo $sport; ?>" data-name="<?php echo $eventName; ?>" data-table-id="<?php echo $evName; ?>ContestantsTable">Contestant</button>
                                <button class="faci loadFacilitatorsBtn" onclick="toggleSubTable('<?php echo $evName; ?>FacilitatorTable')" data-event="<?php echo $ID; ?>" data-personnel="Facilitator" data-type="<?php echo $sport; ?>" data-name="<?php echo $eventName; ?>" data-table-id="<?php echo $evName; ?>FacilitatorTable">Facilitator</button>
                                <button class="scoring loadScoringBtn" onclick="toggleSubTable('<?php echo $evName; ?>ScoringTable')" data-table-id="<?php echo $evName; ?>ScoringTable">Scoring</button>
                            </div>

                            <div id="<?php echo $evName; ?>ContestantTable" class="hidden-table" style="display: none;">
                                <h1>Contestant Table</h1>
                                <table id="<?php echo $evName; ?>ContestantsTable" class="contestantsTable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- ADD CONTESTANT FORM -->
                                    </tbody>
                                </table>
                                <div class="buttons">
                                    <button type="button" class="addcon-btn" onclick="openContiModal(this)" data-type="<?php echo $sport; ?>" data-name="<?php echo $eventName; ?>" data-event="<?php echo $ID; ?>">Add Contestant</button>
                                </div>
                            </div>

                            <div id="<?php echo $evName; ?>FacilitatorTable" class="hidden-table" style="display: none;">
                                <h1>Facilitator Table</h1>
                                <table id="<?php echo $evName; ?>FacilitatorTableContent" class="facilitatorTable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- FACI TABLE -->
                                    </tbody>
                                </table>
                                <div class="buttons">
                                    <button type="button" class="addfaci-btn" onclick="openFaciModal(this)" data-event="<?php echo $ID; ?>" data-personnel="Facilitator" data-type="<?php echo $sport; ?>" data-name="<?php echo $eventName; ?>">Add Facilitator</button>
                                </div>
                            </div>

                            <div id="<?php echo $evName; ?>ScoringTable" class="hidden-table" style="display: none;">
                                <h1>All Scoring Table</h1>
                                <table id="<?php echo $evName; ?>ScoringTableContent">
                                    <thead>
                                        <tr>
                                            <th>Rank No.</th>
                                            <th>Ranking</th>
                                            <th>Individual/Dual</th>
                                            <th>Group</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="buttons">
                                    <button type="button" class="addscore-btn" onclick="openScoreModal(this)">Add Score</button>
                                </div>
                            </div>

                        </div>
                        <?php
                    }
                } else {
                    echo "No events on Sports";
                }
                $result->free();
                $stmt->close();
                ?>
            </div>

        <div>
            <div class="account" onclick="toggleSubEvents('socioSubEvents')">
                <div class="left-deets">
                    <div class="acc-img">
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="acc-deets" id="socioEvent">
                        <p id="name">Socio-Cultural</p>
                    </div>
                </div>
            </div>

            <div class="accounts" id="socioSubEvents" style="display: none;">
                <div class="accounts-title">
                    <p id="event">Events</p>
                </div>

<?php
    //retrieve event (socio)
    $sql = "CALL sp_getEventFrom(?)";
    $socio = "Socio-Cultural";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $socio);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ID = $row['eventID'];
            $socioEvent = str_replace(' ', '', $row['eventName']);
            ?>
                <div class="sub-account" id="<?php echo $socioEvent?>" data-id="<?php echo $ID?>" data-name="<?php echo $row['eventName']?>" data-type="<?php echo $row['eventType']?>" data-category="<?php echo $row['eventCategory']?>">
                    <div class="left-deets" onclick="toggleTable('<?php echo $socioEvent?>Table')">
                        <div class="acc-img">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="acc-deets">
                            <p id="name"><?php echo $row['eventName']?></p>
                        </div>
                    </div>
                    <div class="right-deets">
                        <div class="acc-buttons">
                            <div class="subtrash-icon" onclick="deleteThis(<?php echo $ID ?>)">
                                <i class="fa-solid fa-trash-can"></i>
                            </div>

                            <div class="subedit-icon" onclick="openEditEvModal(this)">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container" id="<?php echo $socioEvent?>Table" style="display: none;">
                    <div class="buttons">
                        <button class="contestant loadContestantsBtn" onclick="toggleSubTable('<?php echo $socioEvent?>ContestantTable')" data-event="<?php echo $ID; ?>" data-type="<?php echo $socio; ?>" data-name="<?php echo $row['eventName']; ?>" data-table-id="<?php echo $socioEvent; ?>ContestantsTable">Contestant</button>
                        <button class="judge loadJudgesBtn" onclick="toggleSubTable('<?php echo $socioEvent?>JudgeTable')" data-event="<?php echo $ID; ?>" data-type="<?php echo $socio; ?>" data-name="<?php echo $row['eventName']; ?>" data-table-id="<?php echo $socioEvent; ?>JudgesTable">Judge</button>
                        <button class="criteria1 loadCriteriaBtn" onclick="toggleSubTable('<?php echo $socioEvent?>CriteriaTable')" data-event="<?php echo $ID; ?>" data-name="<?php echo $row['eventName']; ?>" data-table-id="<?php echo $socioEvent; ?>CriteriaTable">Criteria</button>
                        <button class="tally loadTallyBtn" onclick="toggleSubTable('<?php echo $socioEvent?>TallyTable')">Tally</button>
                        <button class="scoring loadScoringBtn" onclick="toggleSubTable('<?php echo $socioEvent?>ScoringTable')" data-table-id="<?php echo $socioEvent; ?>ScoringTable">Scoring</button>
                    </div>

                    <div id="<?php echo $socioEvent; ?>ContestantTable" class="hidden-table" style="display: none;">
                        <h1>Contestant Table</h1>
                        <table id="<?php echo $socioEvent; ?>ContestantsTable" class="contestantsTable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- ADD CONTESTANT FORM -->
                                    </tbody>
                        </table>
                        <div class="buttons">
                            <button type="button" class="addcon-btn" onclick="openContiModal(this)" data-event="<?php echo $ID; ?>" data-type="<?php echo $socio; ?>" data-name="<?php echo $row['eventName']; ?>">Add Contestant</button>
                        </div>
                    </div>

                    <div id="<?php echo $socioEvent?>JudgeTable" class="hidden-table" style="display: none;">
                        <h1>Judge Table</h1>   
                        <table id="<?php echo $socioEvent?>JudgesTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody> 
                            </tbody>
                        </table>
                        <div class="buttons">
                            <button type="button" class="addjudge-btn" onclick="openJudgeModal(this)" data-event="<?php echo $ID; ?>" data-name="<?php echo $row['eventName']; ?>">Add Judge</button>
                        </div>
                    </div>

                    <div id="<?php echo $socioEvent?>CriteriaTable" class="hidden-table" style="display: none;">
                        <h1>Criteria Table</h1> 
                        <table id="<?php echo $socioEvent?>CriteriasTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Criteria</th>
                                    <th>Percentage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="buttons">
                            <button type="button" class="addcri-btn" onclick="openCriModal(this)" data-event="<?php echo $ID; ?>" data-name="<?php echo $row['eventName']; ?>">Add Criteria</button>
                        </div>
                    </div>

                    <div id="<?php echo $socioEvent?>TallyTable" class="hidden-table" style="display: none;">
                        <h1>Tally Table</h1>
                        <table>
                            <thead>
                                <tr>
                                    <th>Team</th>
                                    <th>Faci</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    
                                    <td>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <i class="fa-solid fa-trash-can"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="buttons">
                            <button class="main-btn">Add</button>
                            <button class="main-btn">Edit Table</button>
                        </div>
                    </div>

                    <div id="<?php echo $socioEvent; ?>ScoringTable" class="hidden-table" style="display: none;">
                        <h1>All Scoring Table</h1>
                        <table id="<?php echo $socioEvent; ?>ScoringTableContent">
                            <thead>
                                <tr>
                                    <th>Rank No.</th>
                                    <th>Ranking</th>
                                    <th>Individual/Dual</th>
                                    <th>Group</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="buttons">
                            <button type="button" class="addscore-btn" onclick="openScoreModal(this)">Add Score</button>
                        </div>
                    </div>
                </div>
            <?php
        }
    } else {
        echo "No events on Socio-Cultural";
    }
    $result->free();
    $stmt->close();
?>
            </div>
        </div>
    </div>


    <!--FOR ADDING AN EVENT MODAL-->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('myModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <p class="addevent">Add Event</p>

                        <div class="form-group">
                            <label for="eventType">Type:</label>
                            <select id="eventType" name="eventType">
                                <option value="Socio-Cultural">Socio-Cultural</option>
                                <option value="Sports">Sports</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="eventName">Event Name:</label>
                            <input type="text" id="eventName" name="eventName">
                        </div>

                        <div class="form-group">
                            <label for="eventCategory">Category:</label>
                            <select id="eventCategory" name="eventCategory">
                                <option value="Individual/Dual">Individual/Dual</option>
                                <option value="Team">Team</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('myModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR EDITING AN EVENT MODAL-->
    <div id="editEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editEventModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form id="editeventForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="editEvent">
                        <p class="addevent">Edit Event</p>
                        <input type="text" id="editeventId" name="editeventId" hidden>

                        <div class="form-group">
                            <label for="editeventType">Type:</label>
                            <select id="editeventType" name="editeventType">
                                <option value="Socio-Cultural">Socio-Cultural</option>
                                <option value="Sports">Sports</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editeventName">Event Name:</label>
                            <input type="text" id="editeventName" name="editeventName">
                        </div>

                        <div class="form-group">
                            <label for="editeventCategory">Category:</label>
                            <select id="editeventCategory" name="editeventCategory">
                                <option value="Individual/Dual">Individual/Dual</option>
                                <option value="Team">Team</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('editEventModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR ADDING CONTESTANT MODAL-->
    <div id="contestandtModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('contestandtModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data" onsubmit="updateNameField()">
                        <input type="hidden" name="action" value="addContestant">
                        <p class="addevent">Add Contestant</p>

                        <div class="form-group">
                            <label for="contestantId">Contestant Name:</label>
                            <select id="contestantId" name="contestantId">
                                <?php
                                    $sql = "CALL sp_getAllTeam;";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $teamval = $row['teamId'];
                                            $teamname = $row['teamName'];
                                            ?>
                                            <option value="<?php echo $teamval; ?>"><?php echo $teamname; ?></option>
                                            <?php
                                        }
                                    }
                                    $result->free();
                                    $stmt->close();
                                ?>
                            </select>
                        </div>

                        <input type="text" id="conEvId" name="conEvId" hidden>
                        <input type="text" id="contestantName" name="contestantName" hidden>
                        <input type="text" id="contestantType" name="contestantType" hidden>
                        <input type="text" id="contestantEVName" name="contestantEVName" hidden>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('contestandtModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!--FOR ADDING FACI MODAL-->
    <div id="faciModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('faciModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data" onsubmit="updateFaciName()">
                        <input type="hidden" name="action" value="addFaci">
                        <p class="addevent">Add Facilitator</p>

                        <div class="form-group">
                            <label for="faciId">Contestant Name:</label>
                            <select id="faciId" name="faciId" required>
                                <?php
                                    $sql = "CALL sp_getAccType(?);";
                                    $stmt = $conn->prepare($sql);
                                    $role = 'Facilitator';
                                    $stmt->bind_param("s", $role);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $userId = $row['userId'];
                                            $name = $row['firstName'];
                                            ?>
                                            <option value="<?php echo $userId; ?>" data-fname="<?php echo $name; ?>"><?php echo $name; ?></option>
                                            <?php
                                        }
                                    }
                                    $result->free();
                                    $stmt->close();
                                ?>
                            </select>
                        </div>

                        <input type="text" id="faciEvId" name="faciEvId" hidden>
                        <input type="text" id="faciEVName" name="faciEVName" hidden>
                        <input type="text" id="faciName" name="faciName" hidden>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('faciModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR ADDING JUDGE MODAL-->
    <div id="judgeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('judgeModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data" onsubmit="updateJudgeField()">
                        <input type="hidden" name="action" value="addJudge">
                        <p class="addevent">Add Judge</p>

                        <div class="form-group">
                            <label for="judgeId">Contestant Name:</label>
                            <select id="judgeId" name="judgeId" required>
                                <?php
                                    $sql = "CALL sp_getAccType(?);";
                                    $stmt = $conn->prepare($sql);
                                    $role = 'Judge';
                                    $stmt->bind_param("s", $role);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $userId = $row['userId'];
                                            $name = $row['firstName'];
                                            ?>
                                            <option value="<?php echo $userId; ?>"><?php echo $name; ?></option>
                                            <?php
                                        }
                                    }
                                    $result->free();
                                    $stmt->close();
                                ?>
                            </select>
                        </div>

                        <input type="text" id="judgeEvId" name="judgeEvId" hidden>
                        <input type="text" id="judgeEVName" name="judgeEVName" hidden>
                        <input type="text" id="judgeName" name="judgeName" hidden>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('judgeModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR ADDING CRITERIA MODAL-->
    <div id="criModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('criModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="addCriteria">
                        <p class="addevent">Add Criteria</p>

                        <div class="form-group">
                            <label for="criteria">Criteria:</label>
                            <textarea name="criteria" id="criteria"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="criPts">Percentage:</label>
                            <input type="number" id="criPts" name="criPts">
                        </div>

                        <input type="number" id="criEVId" name="criEVId" hidden>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('criModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR EDITING CRITERIA MODAL-->
    <div id="editcriModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editcriModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="editCriteria">
                        <p class="addevent">Edit Criteria</p>
                        <input type="number" id="editcriId" name="editcriId" hidden>

                        <div class="form-group">
                            <label for="editcriteria">Criteria:</label>
                            <textarea name="editcriteria" id="editcriteria"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="editcriPts">Points:</label>
                            <input type="number" id="editcriPts" name="editcriPts">
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('editcriModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR ADDING SCORING MODAL-->
    <div id="scoringModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('scoringModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="addScoring">
                        <p class="addevent">Add Scoring</p>

                        <div class="form-group">
                            <label for="rankName">Rank No.:</label>
                            <input type="number" id="rankNo" maxlength="30" name="rankNo" placeholder="e.g., 1">
                        </div>

                        <div class="form-group">
                            <label for="rankName">Rank Name:</label>
                            <input type="text" id="rankName" maxlength="30" name="rankName" placeholder="e.g., Champion/Winner">
                        </div>
                        
                        <div class="form-group">
                            <label for="scoringCategory">Category:</label>
                            <select id="scoringCategory" name="scoringCategory">
                                <option value="Individual/Dual">Individual/Dual</option>
                                <option value="Team">Team</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="scorePts">Points:</label>
                            <input type="number" maxlength="10" id="scorePts" name="scorePts">
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('scoringModal')">Cancel</button>
                            <button type="submit" class="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        function updateNameField() {
            var dropdown = document.getElementById("contestantId");
            var selectedText = dropdown.options[dropdown.selectedIndex].text;
            document.getElementById("contestantName").value = selectedText;
        }

        function updateFaciName() {
            var dropdown = document.getElementById("faciId");
            var selectedText = dropdown.options[dropdown.selectedIndex].text;
            document.getElementById("faciName").value = selectedText;
        }

        function updateJudgeField() {
            var dropdown = document.getElementById("judgeId");
            var selectedText = dropdown.options[dropdown.selectedIndex].text;
            document.getElementById("judgeName").value = selectedText;
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.loadContestantsBtn').forEach(function(button) {
                button.addEventListener('click', function() {
                    loadDoc(button); //load contestant table
                });
            });

            document.querySelectorAll('.loadFacilitatorsBtn').forEach(function(button) {
                button.addEventListener('click', function() {
                    loadFaci(button); //load facilitator table
                });
            });

            document.querySelectorAll('.loadJudgesBtn').forEach(function(button) {
                button.addEventListener('click', function() {
                    loadJudge(button); //load facilitator table
                });
            });

            document.querySelectorAll('.loadCriteriaBtn').forEach(function(button) {
                button.addEventListener('click', function() {
                    loadCriteria(button); //load facilitator table
                });
            });

            document.querySelectorAll('.loadScoringBtn').forEach(function(button) {
                button.addEventListener('click', function() {
                    loadScoring(button); //load scoring table
                });
            });

            //deleting contestant
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-icon')) {
                    var conid = event.target.getAttribute('data-cont');
                    deleteCont(conid);
                }
            });

            //deleting facilitator
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-icon-faci')) {
                    var id = event.target.getAttribute('data-id');
                    deleteFaci(id);
                }
            });

            //deleting judge
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-icon-judge')) {
                    var id = event.target.getAttribute('data-id');
                    deleteJudge(id);
                }
            });

            //deleting judge
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-icon-cri')) {
                    var id = event.target.getAttribute('data-id');
                    deleteCri(id);
                }
            });

            //deleting score
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-icon-pts')) {
                    var name = event.target.getAttribute('data-rank');
                    deleteScoring(name);
                }
            });
        });


        function loadDoc(button) {
            var id = button.getAttribute('data-event');
            var tableId = button.getAttribute('data-table-id')
            
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "get_contestants.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("evId=" + id);
        }

        function loadFaci(button) {
            var evid = button.getAttribute('data-event');
            var evname = button.getAttribute('data-name');
            var tableId = button.getAttribute('data-table-id');
            
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "get_faci.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("evid=" + evid);
        }

        function loadJudge(button) {
            var id = button.getAttribute('data-event');
            var tableId = button.getAttribute('data-table-id');
            
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "get_judge.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("evid=" + id);
        }

        function loadCriteria(button) {
            var evid = button.getAttribute('data-event');
            var tableId = button.getAttribute('data-table-id');
            
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "get_criteria.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("evid=" + evid);
        }

        function loadScoring(button) {
            var tableId = button.getAttribute('data-table-id');
            
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.querySelector("#" + tableId + " tbody").innerHTML = this.responseText;
                }
            };
            xhttp.open("POST", "get_scoring.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send();
        }


        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("openModal").addEventListener("click", function () {
                openModal();
            });

        });

        function openModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
        }

        function openContiModal(element) {
            var card = element.closest('.addcon-btn');
            var eventType = card.getAttribute('data-type');
            var eventName = card.getAttribute('data-name');
            var evId = card.getAttribute('data-event');

            document.getElementById('contestantType').value = eventType;          
            document.getElementById('contestantEVName').value = eventName;
            document.getElementById('conEvId').value = evId;

            var modal = document.getElementById("contestandtModal");
            modal.style.display = "block";
        }

        function openFaciModal(element) {
            var card = element.closest('.addfaci-btn');
            var event = card.getAttribute('data-event');
            var eventName = card.getAttribute('data-name');

            document.getElementById('faciEvId').value = event;          
            document.getElementById('faciEVName').value = eventName;

            var modal = document.getElementById("faciModal");
            modal.style.display = "block";
        }

        function openJudgeModal(element) {
            var card = element.closest('.addjudge-btn');
            var event = card.getAttribute('data-event');
            var eventName = card.getAttribute('data-name');

            document.getElementById('judgeEvId').value = event;          
            document.getElementById('judgeEVName').value = eventName;

            var modal = document.getElementById("judgeModal");
            modal.style.display = "block";
        }

        function openCriModal(element) {
            var card = element.closest('.addcri-btn');
            var eventid = card.getAttribute('data-event');
        
            document.getElementById('criEVId').value = eventid;

            var modal = document.getElementById("criModal");
            modal.style.display = "block";
        }

        function openEditCriModal(element) {
            var card = element.closest('.edit-icon-cri');
            var criId = card.getAttribute('data-id');
            var criteria = card.getAttribute('data-criteria');
            var pts = card.getAttribute('data-pts');
        
            document.getElementById('editcriId').value = criId;
            document.getElementById('editcriteria').value = criteria;
            document.getElementById('editcriPts').value = pts;

            var modal = document.getElementById("editcriModal");
            modal.style.display = "block";
        }

        function openScoreModal(element) {
            var modal = document.getElementById("scoringModal");
            modal.style.display = "block";
        }

        function closeModal(thisModal) {
            var modal = document.getElementById(thisModal);
            modal.style.display = "none";
        }

        function closeEditModal() {
            var modal = document.getElementById("editEventModal");
            modal.style.display = "none";
        }

        function closeEModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }


        function deleteThis(id) {
            window.location.href = 'EventTeam.php?eventid='+id;
        }

        function deleteCont(id) {
            window.location.href = 'EventTeam.php?contid='+id;
        }

        function deleteFaci(id) {
            window.location.href = 'EventTeam.php?faciid='+id;
        }

        function deleteJudge(id) {
            window.location.href = 'EventTeam.php?judgeid='+id;
        }

        function deleteCri(id) {
            window.location.href = 'EventTeam.php?criid='+id;
        }

        function deleteScoring(name) {
            window.location.href = 'EventTeam.php?rank='+name;
        }

        function toggleSubEvents(subEventId) {
            const subEvents = document.getElementById(subEventId);
            subEvents.style.display = subEvents.style.display === 'none' ? 'block' : 'none';
        }

        function toggleTable(tableId) {
            console.log("Toggling table:", tableId);
            const table = document.getElementById(tableId);
            const tables = document.querySelectorAll('.container');
            tables.forEach(t => {
                if (t.id !== tableId) {
                    t.style.display = 'none';
                }
            });
            table.style.display = 'block';
        }

        function toggleSubTable(tableId) {
            console.log("Toggling table:", tableId);
            const table = document.getElementById(tableId);
            const tables = document.querySelectorAll('.hidden-table');
            tables.forEach(t => {
                if (t.id !== tableId) {
                    t.style.display = 'none';
                }
            });
            table.style.display = 'block';
        }
    </script>

    <script>
        function openModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
        }

        function openEditEvModal(element) {
            var card = element.closest('.sub-account');
            var eventID = card.getAttribute('data-id');
            var eventType = card.getAttribute('data-type');
            var eventName = card.getAttribute('data-name');
            var eventCat= card.getAttribute('data-category');

            document.getElementById('editeventId').value = eventID;
            document.getElementById('editeventType').value = eventType;          
            document.getElementById('editeventName').value = eventName;
            document.getElementById('editeventCategory').value = eventCat;          

            var modal = document.getElementById("editEventModal");
            modal.style.display = "block";
        }

        function openEditModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "block";
        }

        function closeEditModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }

        function addProgram() {
            // Add your logic to add a program here
        }

        function saveTeam() {
            // Add your logic to save the team here
        }
    </script>

<?php 
    $conn->close();
?>

</body>

</html>