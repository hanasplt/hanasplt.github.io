<?php 
    require_once '../../config/sessionConfig.php'; // Session Cookie
    require_once '../mandatory/verifyLogin.php'; // Logged in or not

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
        <?php
        if (isset($_SESSION['msg'])) { // For displaying message
            echo '
            <div class="msg" id="msg-container">
                <div class="msg-content">
                    <span style="">
                        <p id="form-msg">' . $_SESSION['msg'] . '</p>
                        <button type="button" id="x-btn">OK</button>
                    </span>
                </div>
            </div>
            ';
            unset($_SESSION['error']);
        }
        ?>
        <div class="form-container" id="form-container">
            <h2>Change Password</h2>
            <p>You are required to change your password.
                <span id="letter" class="status">- <i>Lowercase</i> letter</span><br>
                <span id="capital" class="status">- <i>Capital</i> letter</span><br>
                <span id="number" class="status">- A <i>Number</i></span><br>
                <span id="length" class="status">- A Minimum of <i>8 characters</i></span><br>
            </p>
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
