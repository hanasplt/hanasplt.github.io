<?php 
    require_once '../../config/sessionConfig.php'; // Session Cookie
    require_once '../../src/roles/admin/verifyLoginSession.php'; // Logged in or not

    $fid = $_SESSION['userId'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="icon" href="../../public/assets/icons/logo-1.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../public/assets/css/styles.css">

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Change Password</h2>
            <p>You are required to change your password.</p>
            <form id="changePasswordForm" method="post" action="../../public/process.php">
                <input type="text" name="fid" id="fid" value="<?php echo $fid; ?>" hidden>
                <div class="input-group">
                    <label for="newpass">Enter new password:</label>
                    <input type="password" name="newpass" id="newpass" required>
                    <i class="fa-solid fa-eye-slash" id="toggleNewPass"></i>
                </div>
                <div class="input-group">
                    <label for="confpass">Confirm new password:</label>
                    <input type="password" name="confpass" id="confpass" required>
                    <i class="fa-solid fa-eye-slash" id="toggleConfPass"></i>
                </div>
                <button type="submit"  name="changepassfaci">Change Password</button>
            </form>
        </div>
    </div>

    <script src="../mandatory/js/change_passComt.js"></script>
</body>
</html>
