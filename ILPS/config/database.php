<?php

// Create Database for the system

    // Creating Database ilps
    $servername = "localhost";
    $username = "root";
    $password = "";

    $conn = mysqli_connect($servername, $username, $password);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "CREATE DATABASE IF NOT EXISTS ilps";
    
    if (mysqli_query($conn, $sql)) {}

    else {
        echo "Error creating database: " . mysqli_error($conn);
    }

    mysqli_close($conn);

?>