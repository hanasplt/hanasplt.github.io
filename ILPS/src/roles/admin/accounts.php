<?php
    require_once '../../../config/sessionConfig.php'; // Session Cookie
    $conn = require_once '../../../config/db.php'; // Database connection
    require_once '../admin/verifyLoginSession.php'; // Logged in or not

    $sql = "CALL sp_getAllAcc()"; // retrieving all accounts
    $result = $conn->query($sql);

    $accounts = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
    }
    $result->free_result();
    $conn->next_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Accounts</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../admin/css/accounts.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="icon" href="../../../public/assets/icons/logo.svg">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

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
            <p onclick="window.location.href = 'accounts.php';"><b>Accounts</b></p>
            <p onclick="window.location.href = 'teams.php';">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';">Events</p>
            <p onclick="window.location.href = 'schedule.php';">Schedule</p>
            <p onclick="window.location.href = 'reports.php';">Reports</p>
            <p onclick="window.location.href = '../admin/logs/accesslog.html';">Logs</p>
        </div>
        <div class="menu-icon">
            <i class="fas fa-sign-out-alt" id="logoutIcon"></i>
        </div>
    </div>

    <div class="new-account" id="openPopup">
        <div class="plus-icon">
            <i class="fas fa-plus"></i>
        </div>
        <div class="new-account-info">
            <p id="create">Create a New Account</p>
            <p id="add">Add an account for committee/judge.</p>
        </div>
        <div class="iframe-overlay" id="iframeOverlay">
            <iframe id="popupFrame"></iframe>
        </div>
    </div>

    <div class="accounts">
        <div class="accounts-title">
            <p id="accs">Accounts</p>
            <input type="text" id="searchBox" name="SearchBox" placeholder="Search Account...">
            <div class="dropdowns">
                <div class="sort-by">
                    <select id="sort-type" name="sort-type">
                        <option value="all">All</option>
                        <option value="Committee">Committee</option>
                        <option value="Judge">Judge</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="a-z">
                    <select id="abc" name="abc">
                        <option value="" hidden></option>
                        <option value="a-z">A-Z</option>
                        <option value="z-a">Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <?php
        if (!empty($accounts)) {
            foreach ($accounts as $row) {
                $fullName = $row['firstName'];
                if (!empty($row['middleName'])) {
                    $fullName .= " " . $row['middleName'];
                }
                $fullName .= " " . $row['lastName'];
                if (!empty($row['suffix'])) {
                    $fullName .= " " . $row['suffix'];
                }
                ?>
                <div class="account" data-name="<?php echo strtolower($fullName); ?>">
                    <div class="left-deets">
                        <div class="acc-img">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="acc-deets">
                            <p id="name">
                                <?php
                                echo $row['firstName'];
                                if (!empty($row['middleName'])) {
                                    echo " " . $row['middleName'];
                                }
                                echo " " . $row['lastName'];
                                if (!empty($row['suffix'])) {
                                    echo " " . $row['suffix'];
                                }
                                ?>
                            </p>
                            <p><?php echo $row['type']; ?></p>
                        </div>
                    </div>
                    <div class="acc-buttons">
                        <form method="POST" id="deleteForm_<?php echo $row['userId']; ?>">
                            <input type="hidden" name="userId" value="<?php echo $row['userId']; ?>">
                            <button type="button" class="trash-icon" style="cursor: pointer;" onclick="confirmDelete('<?php echo $row['userId']; ?>')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                        <div class="edit-icon" data-user-id="<?php echo $row['userId']; ?>">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div class="popup" id="popupEdit">
                            <iframe id="editIframe"></iframe>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "0 results"; // no users were added yet.
        }
        $conn->close();
        ?>
    </div>

    <script src="../admin/js/accounts.js"></script>
</body>
</html>
