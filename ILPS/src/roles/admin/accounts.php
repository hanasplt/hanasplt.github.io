<!-- accounts.php -->
<?php
require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php'; // database connection
require_once '../admin/verifyLoginSession.php'; // logged in or not
require_once 'adminPermissions.php'; // Retrieves admin permissions

// Check if search is set, default to '%' (matches everything)
$searchQuery = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%%";
$userId = $_SESSION['userId'];
$main_Admin = 1;

try {
    // Modified SQL query to filter accounts based on search term
    $sql = "SELECT * FROM accounts 
            WHERE (firstName LIKE ? OR middleName LIKE ? OR lastName LIKE ? OR email LIKE ?)
            AND userId != ? AND userId != ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind the search query to the SQL statement (applied to firstName, middleName, lastName, and email)
    $stmt->bind_param("ssssii", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $userId, $main_Admin);
    $stmt->execute();
    $result = $stmt->get_result();

    $accounts = [];
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }

    $result->free_result();
    $stmt->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$conn->close();

?>


<!DOCTYPE html>
<html>

<head>
    <title>Accounts</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin/css/accounts.css">

    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!--Web-logo-->
    <link rel="icon" href="../../../public/assets/icons/logo-top-final.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navigation-bar">
        <img class="logo-img" src="../../../public/assets/icons/ilps-logo.png">
        <nav class="nav-link">
            <p onclick="window.location.href = 'admin.php';" class="navbar" title="Home">Home</p>
            <div class="acc-hover">
                <div class="acc-btn-container">
                    <p onclick="window.location.href = 'accounts.php';" class="navbarbie" ; title="Accounts">Accounts</p>
                </div>
                <div class="account-dropdown">
                    <p onclick="window.location.href = 'roles.php';" class="dc-text">Role</p>
                </div>
            </div>
            <p onclick="window.location.href = 'teams.php';" class="navbar" title="Teams">Teams</p>
            <p onclick="window.location.href = 'EventTeam.php';" class="navbar" title="Events">Events</p>
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
                    <p onclick="window.location.href = 'view-profile.php';" class="dc-text" title="Profile">View Profile</p>
                    <p onclick="window.location.href = 'reports.php';" class="dc-text" title="Reports">Reports</p>
                    <p onclick="window.location.href = 'accesslog.php';" class="dc-text" title="Logs">Logs</p>
                    <p onclick="showConfirmationMsg()" class="dc-text" title="Backup">Backup and Reset</p>
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <?php if (in_array('user_read', $admin_rights)) { ?>
        <?php if (in_array('user_add', $admin_rights)) { ?>
            <!-- Display Create Account Button -->
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
            <!-- Display Message - no permission - create account -->
        <?php } else {
            echo '
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>FYI: </strong> \'Create a New Account\' feature is hidden as you don\'t have the permission.
            </div>
        ';
        } ?>
        <div class="name-export-container">
            <p id="accs">Accounts</p>
            <div class="export-button">
                <form method="post" id="exportForm">
                    <button type="button" onclick="submitForm('export/exportAccXlxs.php')" name="exportlog_xsls" id="exportlog_xsls">
                        Export as Excel
                    </button>
                    <button type="button" onclick="submitForm('export/exportAccpdf.php')" name="exportlog_pdf" id="exportlog_pdf">
                        Export as PDF
                    </button>
                </form>

            </div>
        </div>
        <div class="accounts-title">
            <input type="text" id="searchBox" name="SearchBox" placeholder="Search Account..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                        <option value="">Default</option>
                        <option value="a-z">A-Z</option>
                        <option value="z-a">Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="accounts">

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
                    <div class="account">
                        <div style="cursor: pointer;" class="left-deets" data-name="<?php echo strtolower($fullName); ?>" onclick="showRoleDetails('<?php echo $row['firstName']; ?>', '<?php echo $row['middleName']; ?>', '<?php echo $row['lastName']; ?>', '<?php echo $row['suffix']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['type']; ?>')">
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
                            <?php if (in_array('user_delete', $admin_rights)) { ?>
                                <form action="delete-account.php" method="POST" id="deleteForm_<?php echo $row['userId']; ?>">
                                    <input type="hidden" name="userId" value="<?php echo $row['userId']; ?>">
                                    <button type="button" class="trash-icon" style="cursor: pointer;" onclick="confirmDelete('<?php echo $row['userId']; ?>', '<?php echo $fullName; ?>', event)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            <?php } ?>

                            <?php if (in_array('user_update', $admin_rights)) { ?>
                                <div class="edit-icon" data-user-id="<?php echo $row['userId']; ?>">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="popup" id="popupEdit">
                        <iframe id="editIframe"></iframe>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p>No accounts found matching your search.</p>
                </div>
            <?php
            }
            ?>
        </div>
        <script>
            // Store the original order of accounts and current sort order
            let originalOrder = [];
            let currentSortOrder = "";

            document.addEventListener('DOMContentLoaded', function() {
                let accountsContainer = document.querySelector('.accounts');
                originalOrder = Array.from(accountsContainer.getElementsByClassName('account'));

                // Initialize sort dropdown listener
                document.getElementById('abc').addEventListener('change', function() {
                    currentSortOrder = this.value;
                    sortAccounts(currentSortOrder);
                });

                // Initialize search box listener
                document.getElementById('searchBox').addEventListener('input', function() {
                    let searchTerm = this.value;

                    // Fetch searched accounts and maintain sort order
                    fetch('fetchAccounts.php?search=' + encodeURIComponent(searchTerm))
                        .then(response => response.text())
                        .then(data => {
                            document.querySelector('.accounts').innerHTML = data;
                            // Reapply current sort order after search results are loaded
                            if (currentSortOrder) {
                                sortAccounts(currentSortOrder);
                            }
                        })
                        .catch(error => console.error('Error fetching accounts:', error));
                });
            });

            function sortAccounts(order) {
                let accountsContainer = document.querySelector('.accounts');
                let accounts = Array.from(accountsContainer.getElementsByClassName('account'));

                // If default is selected and no search term, restore original order
                if (order === "" && document.getElementById('searchBox').value === "") {
                    originalOrder.forEach(function(account) {
                        accountsContainer.appendChild(account);
                    });
                    return;
                }

                // Sort the accounts array based on the selected order
                accounts.sort(function(a, b) {
                    let nameA = a.querySelector('.acc-deets #name').textContent.trim().toLowerCase();
                    let nameB = b.querySelector('.acc-deets #name').textContent.trim().toLowerCase();

                    if (order === 'a-z') {
                        return nameA.localeCompare(nameB);
                    } else if (order === 'z-a') {
                        return nameB.localeCompare(nameA);
                    }
                    return 0;
                });

                // Clear the accounts container and append sorted accounts
                accounts.forEach(function(account) {
                    accountsContainer.appendChild(account);
                });
            }
        </script>


        <script src="../admin/js/accounts.js"></script>
    <?php } else {
        echo '
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Oops!</strong> You lack the permission to view \'Accounts\' features.
            </div>
        ';
    } ?>
</body>

</html>