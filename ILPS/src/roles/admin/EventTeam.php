<?php require_once 'EventTeamProcess.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>

    <link rel="stylesheet" href="../admin/css/EventTeam.css">

    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <!--alert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
            <p onclick="window.location.href = 'admin.php';" class="navbar" title="Home">Home</p>
            <div class="acc-hover">
                <div class="acc-btn-container">
                    <p onclick="window.location.href = 'accounts.php';" class="navbar" ; title="Accounts">Accounts</p>
                </div>
                <div class="account-dropdown">
                    <p onclick="window.location.href = 'roles.php';" class="dc-text">Role</p>
                </div>
            </div>
            <p onclick="window.location.href = 'teams.php';" class="navbar" title="Teams">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="navbarbie" title="Events">Events</p>
            <p onclick="window.location.href = 'schedule.php';" class="navbar" title="Schedule">Schedule</p>
        </nav>
        <nav class="nav-link-1">
            <div class="dropdown">
                <button class="dropbtn">
                    <img class="icon-img" src="../../../public/assets/icons/icon-user.jpg">
                    <div>
                        <p class="user-name"><?php echo $admin_name; ?></p>
                        <p class="administrator">ADMINISTRATOR</p>
                    </div>
                </button>
                <div class="dropdown-content">
                    <p onclick="window.location.href = '';" class="dc-text" title="Profile">View Profile</p>
                    <p onclick="window.location.href = 'reports.php';" class="dc-text" title="Reports">Reports</p>
                    <p onclick="window.location.href = 'accesslog.php';" class="dc-text" title="Logs">Logs</p>
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <?php if (in_array('event_read', $admin_rights)) { ?>
        <?php if (in_array('event_add', $admin_rights)) { ?>
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
        <?php } else {
            echo '
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>FYI: </strong> \'Create an Event\' feature is hidden as you don\'t have the permission.
            </div>
        ';
        } ?>
        <div class="accounts">
            <div class="accounts-title">
                <div>
                    <p id="event">Events</p>
                </div>
            </div>
            <div class="iframe-overlay" id="iframeOverlay">
                <iframe id="popupFrame"></iframe>
            </div>
            <div class="button-group">
                <?php if (in_array('contestant_add', $admin_rights)) { ?>
                    <button class="group-btn" onclick="openContestantModal(this)">
                        <i class="fa-solid fa-user-plus"></i> Add Contestant
                    </button>
                <?php } ?>
                <?php if (in_array('committee_add', $admin_rights)) { ?>
                    <button class="group-btn" onclick="openCommitteeModal(this)">
                        <i class="fa-solid fa-users"></i> Add Committee
                    </button>
                <?php } ?>
                <?php if (in_array('judge_add', $admin_rights)) { ?>
                    <button class="group-btn" onclick="openJudgesModal(this)">
                        <i class="fa-solid fa-gavel"></i> Add Judge
                    </button>
                <?php } ?>
                <?php if (in_array('criteria_add', $admin_rights)) { ?>
                    <button class="group-btn" id="openCriteriaPopup">
                        <i class="fa-solid fa-list-check"></i> Add Criteria
                    </button>
                <?php } ?>
                <?php if (in_array('scoring_read', $admin_rights)) { ?>
                    <button class="group-btn" onclick="openScoringTable(this)">
                        <i class="fa-solid fa-chart-bar"></i> Scoring Table
                    </button>
                <?php } ?>
            </div>
            <div class="details">
                <div class="account-header">
                    <div style="float: left; width: 25%;">Event Name</div>
                    <div style="float: left; width: 15%;">Type</div>
                    <div style="float: left; width: 11%;">Category</div>
                    <div>Action</div>
                </div>

                <?php

                try {
                    // Retrieve All Events
                    $getAllEvents = "CALL sp_getEvents()";

                    $stmt = $conn->prepare($getAllEvents);
                    $stmt->execute();

                    $retval = $stmt->get_result();

                    if ($retval->num_rows > 0) {
                        while ($row = $retval->fetch_assoc()) {
                            // Display Event information
                            $db_evID = $row['eventID'];
                            $db_evName = str_replace(' ', '', $row['eventName']);
                            $db_evType = $row['eventType'];
                            $db_evCatg = $row['eventCategory'];

                ?>
                            <div class="account" onclick="toggleEvent('eventTable<?php echo $db_evName; ?>', 
                        '<?php echo $db_evType; ?>', '<?php echo $db_evID; ?>',
                        '<?php echo $row['eventName']; ?>', 'displayContestantTable<?php echo $db_evName; ?>', 
                        'displayOtherTable<?php echo $db_evName; ?>', 'displayCriteriaTable<?php echo $db_evName; ?>')"
                                data-event-id="<?php echo $db_evID; ?>"
                                data-name="<?php echo $row['eventName']; ?>"
                                data-table-id="<?php echo $db_evName; ?>Table"
                                data-type="<?php echo $db_evType; ?>"
                                data-category="<?php echo $db_evCatg; ?>">
                                <div style="float: left; width: 25%;"><?php echo $row['eventName']; ?></div>
                                <div style="float: left; width: 15%;"><?php echo $db_evType; ?></div>
                                <div style="float: left; width: 10%;"><?php echo $db_evCatg; ?></div>
                                <div class="acc-buttons">
                                    <?php if (in_array('event_delete', $admin_rights)) { ?>
                                        <div class="subtrash-icon" onclick="deleteThis(<?php echo $db_evID; ?>, '<?php echo $db_evName; ?>')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </div>
                                    <?php } ?>

                                    <?php if (in_array('event_update', $admin_rights)) { ?>
                                        <div class="subedit-icon" onclick="openEditEventModal(this)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="container" id="eventTable<?php echo $db_evName; ?>" style="display: none;" data-event-id="<?php echo $db_evID; ?>" data-table-id="<?php echo $db_evName; ?>Table">
                                <!-- EXPORT BUTTONS -->
                                <div class="export-button">
                                    <form method="post" id="exportForm<?php echo $db_evID; ?>">
                                        <input type="hidden" name="xp_eventId" value="<?php echo $db_evID; ?>">
                                        Export <?php echo $db_evName; ?>'s Data as:
                                        <button type="button" onclick="submitForm(<?php echo $db_evID; ?>, 'export/exportEventXlxs.php')" name="exportlog_xsls" id="exportlog_xsls">
                                            Excel
                                        </button>
                                        <button type="button" onclick="submitForm(<?php echo $db_evID; ?>, 'export/exportEventpdf.php')" name="exportlog_pdf" id="exportlog_pdf">
                                            PDF
                                        </button>
                                    </form>
                                </div>

                                <!-- TABLES -->
                                <div id="displayContestantTable<?php echo $db_evName; ?>"></div>
                                <div id="displayOtherTable<?php echo $db_evName; ?>"></div>
                                <div id="displayCriteriaTable<?php echo $db_evName; ?>"></div>
                            </div>
                <?php

                        }
                    } else {
                        echo '
                    <div class="container">
                        <b>No events.</b>
                    </div>
                    ';
                    }

                    $retval->free();
                    $stmt->close();
                } catch (Exception $e) {
                    die("Error: " . $e->getMessage());
                }

                ?>

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
                                <label for="eventType">Type</label>
                                <select id="eventType" name="eventType">
                                    <option value="Socio-Cultural">Socio-Cultural</option>
                                    <option value="Sports">Sports</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="eventName">Event Name</label>
                                <input type="text" id="eventName" name="eventName">
                            </div>

                            <div class="form-group">
                                <label for="eventCategory">Category</label>
                                <select id="eventCategory" name="eventCategory">
                                    <option value="Individual/Dual">Individual/Dual</option>
                                    <option value="Team">Team</option>
                                </select>
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
                                <label for="editeventType">Type</label>
                                <select id="editeventType" name="editeventType">
                                    <option value="Socio-Cultural">Socio-Cultural</option>
                                    <option value="Sports">Sports</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editeventName">Event Name</label>
                                <input type="text" id="editeventName" name="editeventName">
                            </div>

                            <div class="form-group">
                                <label for="editeventCategory">Category</label>
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
        <div id="contModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('contModal')">&times;</span>
                <div class="modal-body">
                    <div class="form-section">
                        <form method="post" id="addContForm" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="addContestant">
                            <input type="hidden" name="selectedEventText" id="selectedEventText">
                            <input type="text" id="contestantName" name="contestantName" hidden>
                            <p class="addevent">Add Contestant</p>

                            <div class="form-group">
                                <label for="eventId">Event Name</label>
                                <select id="eventId" name="eventId" required>
                                    <?php
                                    $sql = "CALL sp_getEvents();";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $db_evval = $row['eventID'];
                                            $db_evname = $row['eventName'];
                                            $db_evType = $row['eventType'];

                                    ?>
                                            <option value="<?php echo $db_evval; ?>" data-type="<?php echo $db_evType; ?>">
                                                <?php echo $db_evname; ?>
                                            </option>
                                    <?php
                                        }
                                    } else {
                                        echo '<option selected disabled value=0>No Event/s exists.</option>';
                                    }
                                    $result->free();
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <div class="form-group" id="contestantNumField" style="display: none;">
                                <label for="contNum">Contestant No.</label>
                                <input type="number" name="contNum" id="contNum" required>
                            </div>

                            <div class="form-group">
                                <label for="contestantId">Contestant Name</label>
                                <select id="contestantId" name="contestantId" required>
                                    <?php
                                    $sql = "CALL sp_getAllTeam;";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $teamval = $row['teamId'];
                                            $teamname = $row['teamName'];
                                    ?>
                                            <option value="<?php echo $teamval; ?>"><?php echo $teamname; ?></option>
                                    <?php
                                        }
                                    } else {
                                        echo '<option selected disabled value=0>No Team/s exists.</option>';
                                    }
                                    $result->free();
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="cancel-btn" onclick="closeModal('contModal')">Cancel</button>
                                <button type="submit" class="save-btn-contestant">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--FOR ADDING COMMITTEE (Sports) MODAL-->
        <div id="comtSpModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('comtSpModal')">&times;</span>
                <div class="modal-body">
                    <div class="form-section">
                        <form method="post" id="addCommitteeForm" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="addComt">
                            <p class="addevent">Add Committee</p>

                            <div class="form-group">
                                <label for="eventIdComt">Event Name</label>
                                <select id="eventIdComt" name="eventIdComt" required>
                                    <?php
                                    $sql = "CALL sp_getEventFrom(?);";
                                    $ev_type = "Sports";

                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $ev_type);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $db_evval = $row['eventID'];
                                            $db_evname = $row['eventName'];
                                            $db_evType = $row['eventType'];

                                    ?>
                                            <option value="<?php echo $db_evval; ?>" data-type="<?php echo $db_evType; ?>">
                                                <?php echo $db_evname; ?>
                                            </option>
                                    <?php
                                        }
                                    } else {
                                        echo '<option selected disabled value=0>No Event/s exists.</option>';
                                    }
                                    $result->free();
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="comtId">Committee Name</label>
                                <select id="comtId" name="comtId" required>
                                    <?php
                                    $sql = "CALL sp_getAccType(?);";
                                    $stmt = $conn->prepare($sql);
                                    $role = 'Committee';
                                    $stmt->bind_param("s", $role);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $userId = $row['userId'];
                                            $name = $row['firstName'];
                                            $lname = $row['lastName'];
                                            $fullname = $name . ' ' . $lname;
                                    ?>
                                            <option value="<?php echo $userId; ?>"
                                                data-fname="<?php echo $fullname; ?>"><?php echo $fullname; ?>
                                            </option>
                                    <?php
                                        }
                                    } else {
                                        echo '<option selected disabled value=0>No Committee/s exists.</option>';
                                    }
                                    $result->free();
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <input type="text" id="comtEVName" name="comtEVName" hidden>
                            <input type="text" id="comtName" name="comtName" hidden>

                            <div class="modal-footer">
                                <button type="button" class="cancel-btn" onclick="closeModal('comtSpModal')">
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
        <div id="judgesModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('judgesModal')">&times;</span>
                <div class="modal-body">
                    <div class="form-section">
                        <form method="post" id="addJudgesForm" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="addJudge">
                            <p class="addevent">Add Judge</p>

                            <div class="form-group">
                                <label for="eventIdJ">Event Name</label>
                                <select id="eventIdJ" name="eventIdJ" required>
                                    <?php
                                    $sql = "CALL sp_getEventFrom(?);";
                                    $ev_type = "Socio-Cultural";

                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $ev_type);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $db_evval = $row['eventID'];
                                            $db_evname = $row['eventName'];
                                            $db_evType = $row['eventType'];

                                    ?>
                                            <option value="<?php echo $db_evval; ?>" data-type="<?php echo $db_evType; ?>">
                                                <?php echo $db_evname; ?>
                                            </option>
                                    <?php
                                        }
                                    } else {
                                        echo '<option selected disabled value=0>No Event/s exists.</option>';
                                    }
                                    $result->free();
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="judgeId">Judge Name</label>
                                <select id="judgeId" name="judgeId" required>
                                    <?php
                                    $sql = "CALL sp_getAccType(?);";
                                    $stmt = $conn->prepare($sql);
                                    $role = 'Judge';
                                    $stmt->bind_param("s", $role);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $userId = $row['userId'];
                                            $name = $row['firstName'];
                                            $lname = $row['lastName'];
                                            $fullname = $name . ' ' . $lname;
                                    ?>
                                            <option value="<?php echo $userId; ?>"><?php echo $fullname; ?></option>
                                    <?php
                                        }
                                    } else {
                                        echo '<option selected disabled value=0>No Judge/s exists.</option>';
                                    }
                                    $result->free();
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <input type="text" id="judgeEVName" name="judgeEVName" hidden>
                            <input type="text" id="judgeName" name="judgeName" hidden>

                            <div class="modal-footer">
                                <button type="button" class="cancel-btn" onclick="closeModal('judgesModal')">Cancel</button>
                                <button type="submit" class="save-btn-judge">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--FOR SCORING TABLE MODAL-->
        <div id="eventScoringTable" class="modal">
            <div class="modal-contentt">
                <span class="close" onclick="closeModal('eventScoringTable')">&times;</span>
                <div class="title">
                    <p class="addevent">Scoring Table</p>
                </div>
                <table id="eventScoringTableContent">
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
                        <!-- EVENT SCORING DATA -->
                    </tbody>
                </table>
                <div class="buttons">
                    <button type="button" id="cancelBtn" onclick="closeModal('eventScoringTable')">Cancel</button>
                    <?php if (in_array('scoring_add', $admin_rights)) { ?>
                        <button type="button" id="saveBtn" onclick="openScoreModal()">Add</button>
                    <?php } ?>
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
                                <label for="rankName">Rank No.</label>
                                <input type="number" id="rankNo" name="rankNo" placeholder="e.g., 1">
                            </div>

                            <div class="form-group">
                                <label for="rankName">Rank Name</label>
                                <input type="text" id="rankName" maxlength="30" name="rankName" placeholder="e.g., Champion/Winner">
                            </div>

                            <div class="form-group">
                                <label for="scoringCategory">Category</label>
                                <select id="scoringCategory" name="scoringCategory">
                                    <option value="Individual/Dual">Individual/Dual</option>
                                    <option value="Team">Team</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="scorePts">Points</label>
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
    } else {
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view \'Events\' features.
            </div>
        ';
    }
    $conn->close();
    ?>
</body>

</html>