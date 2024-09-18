<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    $conn = include 'db.php'; // Include database connection

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        // Retrieve data from the Database
        $getLogs = "CALL sp_displayLog";

        $stmt = $conn->prepare($getLogs);
        $stmt->execute();

        $retval = $stmt->get_result();

        if ($retval->num_rows > 0) {
            while ($row = $retval->fetch_assoc()) {
                echo '<tr>';
                    echo '<td>'.$row['logId'].'</td>';
                    echo '<td>'.$row['date_on'].'</td>';
                    echo '<td>'.$row['actions'].'</td>';
                echo '</tr>';
            }
        }
    }

    $conn->close();
?>