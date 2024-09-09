<?php
session_start();

$conn = include 'db.php';

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT userId FROM accounts WHERE reset_token = ?";

    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("s", $token);
    $stmt -> execute();
    $result = $stmt->get_result();

    if($result -> num_rows > 0) {
        $row = $result->fetch_assoc();

        $id = $row['userId'];
        //fetch ang userId para ma update ang password
        // update token to null
        // create form for change password
    }
}

// update new password here {}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="icon" href="assets/icons/logo-1.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Change Password</h2>
            <p>You are required to change your password.</p>
            <form id="changePasswordForm" method="post" action="process.php">
                <input type="text" name="id" id="id" value="<?php echo $id; ?>" hidden>
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
                <button type="submit" name="changepass" class="save-btn">Change Password</button>
            </form>
        </div>
    </div>

    <script src="proceed-reset-pass.js"></script>
</body>
</html>