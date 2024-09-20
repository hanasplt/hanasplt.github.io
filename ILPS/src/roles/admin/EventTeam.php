<?php require_once 'EventTeamProcess.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin/css/EventTeam.css">

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
    <link rel="icon" href="../../../public/assets/icons/logo-1.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <!--others-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="nav-bar">
        <img class="logo-img" src="../../../public/assets/icons/logoo.png">
        <div class="logo-bar">
            <p>Intramural Leaderboard</p>
            <p>and Points System</p>
            <p id="administrator"><i>ADMINISTRATOR</i></p>
        </div>

        <div class="links">
            <p onclick="window.location.href = 'admin.php';">Home</p>
            <p onclick="window.location.href = 'accounts.php';">Accounts</p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';"><b>Events</b></p>
            <p onclick="window.location.href = 'schedule.php';">Schedule</p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
            <p onclick="window.location.href = '../admin/logs/accesslog.html';">Logs</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
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
                                <div class="acc-deetss">
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
                                <button class="contestant loadContestantsBtn" 
                                        onclick="toggleSubTable('<?php echo $evName; ?>ContestantTable')" 
                                        data-event="<?php echo $ID; ?>" data-type="<?php echo $sport; ?>" 
                                        data-name="<?php echo $eventName; ?>" 
                                        data-table-id="<?php echo $evName; ?>ContestantsTable">
                                    Contestant
                                </button>
                                <button class="faci loadFacilitatorsBtn" 
                                        onclick="toggleSubTable('<?php echo $evName; ?>FacilitatorTable')" 
                                        data-event="<?php echo $ID; ?>" data-personnel="Committee" 
                                        data-type="<?php echo $sport; ?>" data-name="<?php echo $eventName; ?>" 
                                        data-table-id="<?php echo $evName; ?>FacilitatorTable">
                                    Committee
                                </button>
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
                                    <button type="button" class="addcon-btn" onclick="openContiModal(this)" 
                                            data-type="<?php echo $sport; ?>" 
                                            data-name="<?php echo $eventName; ?>" 
                                            data-event="<?php echo $ID; ?>">
                                                Add Contestant
                                    </button>
                                </div>
                            </div>

                            <div id="<?php echo $evName; ?>FacilitatorTable" class="hidden-table" 
                            style="display: none;">
                                <h1>Committee Table</h1>
                                <table id="<?php echo $evName; ?>FacilitatorTableContent" class="facilitatorTable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- COMMITEE TABLE -->
                                    </tbody>
                                </table>
                                <div class="buttons">
                                    <button type="button" class="addfaci-btn" onclick="openFaciModal(this)" 
                                            data-event="<?php echo $ID; ?>" data-personnel="Committee" 
                                            data-type="<?php echo $sport; ?>" data-name="<?php echo $eventName; ?>">
                                        Add Committee
                                    </button>
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
                        <div class="acc-deetss">
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
                    <form method="post" id="addEvForm" enctype="multipart/form-data">
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

                        <div class="form-group">
                            <input type="checkbox" name="eventBracket" id="eventBracket" value="Single">
                            Single Elimination
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('myModal')">Cancel</button>
                            <button type="submit" class="save-btn-event">Save</button>
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
                            <button type="submit" class="save-btn-editev">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR ADDING CONTESTANT MODAL -->
    <div id="contestandtModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('contestandtModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" id="addContForm" enctype="multipart/form-data" onsubmit="updateNameField()">
                        <input type="hidden" name="action" value="addContestant">
                        <p class="addevent">Add Contestant</p>

                        <div class="form-group">
                            <label for="contestantId">Contestant Name:</label>
                            <select id="contestantId" name="contestantId" required>
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
                            <button type="submit" class="save-btn-contestant">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--FOR ADDING CONTESTANT MODAL in Socio -->
    <div id="contestantModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('contestantModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" id="addContForm" enctype="multipart/form-data" onsubmit="updateNameField()">
                        <input type="hidden" name="action" value="addContestant">
                        <p class="addevent">Add Contestant</p>

                        <div class="form-group">
                            <label for="contno">Contestant No.:</label>
                            <input type="number" name="contno" id="contno" required>
                            <label for="contestantId">Contestant Name:</label>
                            <select id="contestantId" name="contestantId" required>
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
                            <button type="submit" class="save-btn-contestant">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!--FOR ADDING COMT MODAL-->
    <div id="faciModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('faciModal')">&times;</span>
            <div class="modal-body">
                <div class="form-section">
                    <form method="post" id="addComtForm" enctype="multipart/form-data" onsubmit="updateFaciName()">
                        <input type="hidden" name="action" value="addComt">
                        <p class="addevent">Add Committee</p>

                        <div class="form-group">
                            <label for="comtId">Contestant Name:</label>
                            <select id="comtId" name="comtId" required>
                                <?php
                                    $sql = "CALL sp_getAccType(?);";
                                    $stmt = $conn->prepare($sql);
                                    $role = 'Committee';
                                    $stmt->bind_param("s", $role);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $userId = $row['userId'];
                                            $name = $row['firstName'];
                                            ?>
                                            <option value="<?php echo $userId; ?>" 
                                                    data-fname="<?php echo $name; ?>"><?php echo $name; ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    $result->free();
                                    $stmt->close();
                                ?>
                            </select>
                        </div>

                        <input type="text" id="comtEvId" name="comtEvId" hidden>
                        <input type="text" id="comtEVName" name="comtEVName" hidden>
                        <input type="text" id="comtName" name="comtName" hidden>
                        
                        <div class="modal-footer">
                            <button type="button" class="cancel-btn" onclick="closeModal('faciModal')">
                                Cancel
                            </button>
                            <button type="submit" class="save-btn-comt">Save</button>
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
                    <form method="post" id="addJudgeForm" enctype="multipart/form-data" onsubmit="updateJudgeField()">
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
                            <button type="submit" class="save-btn-judge">Save</button>
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
                    <form method="post" id="addCriForm" enctype="multipart/form-data">
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
                            <button type="submit" class="save-btn-cri">Save</button>
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
                    <form method="post" id="editCriForm" enctype="multipart/form-data">
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
                            <button type="submit" class="save-btn-editcri">Save</button>
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
                    <form method="post" id="addScoringForm" enctype="multipart/form-data">
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
                            <button type="submit" class="save-btn-scr">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="../admin/js/EventTeamJS.js"></script>

<?php 
    $conn->close();
?>

</body>
</html>