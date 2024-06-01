<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = "chestechshopdb";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    printf("Connection failed: %s\n", mysqli_connect_error());
    exit();
}

// Optional: Set the charset to utf8
$conn->set_charset("utf8");
?>