
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $token = bin2hex(random_bytes(32)); // Generate a unique token

    // Check if username exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, store the token
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE username = ?");
        $stmt->bind_param("ss", $token, $username);
        if ($stmt->execute()) {
            // Display the reset link to the user (or send via SMS if phone number is available)
            echo "Password reset link: <a href='reset_password.php?token=$token'>Reset Password</a>";
        } else {
            echo "Failed to store the token.";
        }
    } else {
        echo "Username does not exist.";
    }
    $stmt->close();
    $conn->close();
}
?>