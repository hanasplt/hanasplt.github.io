<?php
session_start();
include '../encryption.php';
$conn = include '../db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Get token and validate
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT userId, reset_token_expiration FROM accounts WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['userId'];

        date_default_timezone_set('Asia/Manila');

        if (strtotime($row['reset_token_expiration']) <= time()) {
            $_SESSION['validate'] = "Token has expired!";
            header("Location: proceed-reset-pass.php");
            exit;
        }
    } else {
        $_SESSION['validate'] = "Token not found!";
        header("Location: proceed-reset-pass.php");
        exit;
    }
}

// Check for POST request to update the password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {

    $id = $_POST['id'];
    $newpass = encrypt($_POST['newpass'], $encryption_key);

    $sql = "CALL sp_resetAccPass(?, ?, NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $newpass);

    if ($stmt->execute()) {
        // Return success response as JSON
        echo json_encode([
            'status' => 'success',
            'message' => 'Password Changed Successfully!'
        ]);
    } else {
        // Handle SQL error
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update password!'
        ]);
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS</title>
    <link rel="icon" href="../assets/icons/logo-1.png">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/styles.css">

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2>Reset Your Password</h2>
            <p>
                Create a strong password. <br>
                <b>Password must contain the following:</b><br>
                <span id="letter" class="status">- <i>Lowercase</i> letter</span><br>
                <span id="capital" class="status">- <i>Capital</i> letter</span><br>
                <span id="number" class="status">- A <i>Number</i></span><br>
                <span id="length" class="status">- A Minimum of <i>8 characters</i></span><br>
            </p>
            <form id="changePasswordForm" method="post">
                <input type="text" name="id" id="id" value="<?php echo $id; ?>" hidden>
                <div class="input-group">
                    <label for="newpass">Enter new password:</label>
                    <input type="password" name="newpass" id="newpass" required>
                    <i class="fa-solid fa-eye-slash" id="toggleNewPass"></i>
                </div>
                <div class="input-group">
                    <label for="confpass">Confirm new password:</label>
                    <input type="password" name="confpass" id="confpass" required disabled>
                    <i class="fa-solid fa-eye-slash" id="toggleConfPass"></i>
                </div>
                <button type="submit" name="changepass" class="save-btn">Change Password</button>
            </form>
        </div>
    </div>
    <script>
        var msg = "<?= $_SESSION['validate'] ?? ''; ?>";

        if (msg != '') {
            Swal.fire({
                title: "Oops..",
                text: msg,
                icon: "error"
            }).then(() => {
                window.location.href = '../login.php'; // Redirect to Login page
            });
            <?php unset($_SESSION['validate']); ?>
        }
    </script>

    <!-- password validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newpassInput = document.getElementById('newpass');
            const letter = document.getElementById('letter');
            const capital = document.getElementById('capital');
            const number = document.getElementById('number');
            const length = document.getElementById('length');

            newpassInput.addEventListener('input', function() {
                const value = newpassInput.value;
                const lowercaseRegex = /[a-z]/;
                const uppercaseRegex = /[A-Z]/;
                const numberRegex = /[0-9]/;
                const minLength = 8;

                // Check for lowercase letter
                if (lowercaseRegex.test(value)) {
                    letter.innerHTML = '✔ <i>Lowercase</i> letter';
                    letter.classList.remove('invalid');
                    letter.classList.add('valid');
                } else if (value.length > 0) {
                    letter.innerHTML = '✘ <i>Lowercase</i> letter';
                    letter.classList.remove('valid');
                    letter.classList.add('invalid');
                } else {
                    letter.innerHTML = '- <i>Lowercase</i> letter';
                    letter.classList.remove('valid', 'invalid');
                }

                // Check for uppercase letter
                if (uppercaseRegex.test(value)) {
                    capital.innerHTML = '✔ <i>Capital</i> letter';
                    capital.classList.remove('invalid');
                    capital.classList.add('valid');
                } else if (value.length > 0) {
                    capital.innerHTML = '✘ <i>Capital</i> letter';
                    capital.classList.remove('valid');
                    capital.classList.add('invalid');
                } else {
                    capital.innerHTML = '- <i>Capital</i> letter';
                    capital.classList.remove('valid', 'invalid');
                }

                // Check for number
                if (numberRegex.test(value)) {
                    number.innerHTML = '✔ A <i>Number</i>';
                    number.classList.remove('invalid');
                    number.classList.add('valid');
                } else if (value.length > 0) {
                    number.innerHTML = '✘ A <i>Number</i>';
                    number.classList.remove('valid');
                    number.classList.add('invalid');
                } else {
                    number.innerHTML = '- A <i>Number</i>';
                    number.classList.remove('valid', 'invalid');
                }

                // Check for length
                if (value.length >= minLength) {
                    length.innerHTML = '✔ A Minimum of <i>8 characters</i>';
                    length.classList.remove('invalid');
                    length.classList.add('valid');
                } else if (value.length > 0) {
                    length.innerHTML = '✘ A Minimum of <i>8 characters</i>';
                    length.classList.remove('valid');
                    length.classList.add('invalid');
                } else {
                    length.innerHTML = '- A Minimum of <i>8 characters</i>';
                    length.classList.remove('valid', 'invalid');
                }


                // Enable confirm password input if new password is valid
                const confirmPassInput = document.getElementById('confpass');
                if (value.length >= 8 && lowercaseRegex.test(value) && uppercaseRegex.test(value) && numberRegex.test(value)) {
                    confirmPassInput.disabled = false;
                } else {
                    confirmPassInput.disabled = true;
                }
            });
        });
    </script>

    <script src="proceed-reset-pass.js"></script>
</body>

</html>