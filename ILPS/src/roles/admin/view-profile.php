<?php
require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php'; // database connection
require_once '../admin/verifyLoginSession.php'; // logged in or not
require_once 'adminPermissions.php'; // Retrieves admin permissions

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];
    $_SESSION['userId'] = $user_id;
}

$getAdmin = "CALL sp_getAnAcc(?)";

$iddd = $_SESSION['userId'];
$stmt = $conn->prepare($getAdmin);
$stmt->bind_param("i", $iddd);
$stmt->execute();
$retname = $stmt->get_result();

// Retrieve Admin Name
$row = $retname->fetch_assoc();
$admin_name = $row['firstName'];

// Retrieve Account Details
$user_ID = $row['userId'];
$first_name = $row['firstName'];
$middle_name = $row['middleName'];
$last_name = $row['lastName'];
$suffix = $row['suffix'];
$email = $row['email'];
$password = $row['password'];
$type = $row['type'];

// Mask password with asterisks
$masked_password = str_repeat('•', strlen($password));

$retname->free();
$stmt->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Account</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin/css/view-profile.css">

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

    <!-- Sweet Alert -->
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
                    <div class="menu-icon">
                        <p id="logout" title="Logout">Logout</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <p class="container-title">Account Settings</p>
        <div class="popup" id="popupEdit">
            <iframe id="editIframe"></iframe>
        </div>
        <div class="profile-container">
            <div class="profile-info">
                <div class="cont-left">
                    <p class="container-p">Basic Info</p>
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($first_name); ?>" readonly>

                    <label for="middleName">Middle Name</label>
                    <input type="text" id="middleName" name="middleName" value="<?php echo htmlspecialchars($middle_name); ?>" readonly>

                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($last_name); ?>" readonly>

                    <label for="suffix">Suffix</label>
                    <input type="text" id="suffix" name="suffix" value="<?php echo htmlspecialchars($suffix); ?>" readonly>
                </div>
                <div class="cont-right">
                    <p class="container-p">Account Info</p>
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>

                    <label for="password">Password</label>
                    <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($masked_password); ?>" readonly>

                    <label for="sort">Type</label>
                    <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($type); ?>" readonly>
                </div>
            </div>
            <div class="button-group">
                <button type="button" id="editBtn" class="edit-button" data-user-id="<?php echo $row['userId']; ?>">Edit Account</button>
            </div>
        </div>
    </div>
    <script src="../admin/js/view-profile.js"></script>
</body>

</html>