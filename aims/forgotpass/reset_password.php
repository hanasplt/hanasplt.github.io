<!-- reset_password.php -->
<?php
session_start();
$servername = "localhost"; 
    $username = "root"; 
    $password = "";
    $dbname = "ilps";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    // Validate the token
    $stmt = $conn->prepare("SELECT username FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, update the password
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $new_password, $token);
        if ($stmt->execute()) {
            echo "Password has been reset successfully.";
        } else {
            echo "Failed to reset the password.";
        }
    } else {
        echo "Invalid token.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>

<?php if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($token)): ?>
<form action="reset_password.php" method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <label for="new_password">Enter new password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <button type="submit">Reset Password</button>
</form>
<?php endif; ?>
