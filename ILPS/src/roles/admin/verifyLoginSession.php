<?php
// Unsent and destory all session stored for security purposes
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../../../public/login.php');
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../../public/login.php'); // Redirect to login page if not logged in
    exit;
}
?>