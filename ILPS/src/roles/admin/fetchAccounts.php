<!-- fetchAccounts.php -->
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
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<html lang="en">

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
<?php if (in_array('user_read', $admin_rights)) { ?>
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
                    <div class="left-deets" style="cursor: pointer;" class="left-deets" data-name="<?php echo strtolower($fullName); ?>" onclick="showRoleDetails('<?php echo $row['firstName']; ?>', '<?php echo $row['middleName']; ?>', '<?php echo $row['lastName']; ?>', '<?php echo $row['suffix']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['type']; ?>')">
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