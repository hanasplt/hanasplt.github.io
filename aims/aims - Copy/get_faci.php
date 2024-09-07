<?php

$conn = include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evId = $_POST['evid'];

    $sql = "CALL sp_getEventComt(?)";    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evId);    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $id = $row['comNo'];
            $name = $row['firstName'];

            echo '<tr>';
            echo '<td>' . $id . '</td>';
            echo '<td>' . $name . '</td>';
            echo '<td><i class="fa-solid fa-trash-can delete-icon-faci" data-id="'.$id.'" style="cursor: pointer;"></i></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No Committee.</td></tr>';
    }
    $result->free();
    $stmt->close();
}
?>
