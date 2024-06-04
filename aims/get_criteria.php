<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ilps";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $evid = $_POST['evid'];

    $sql = "CALL sp_getCriteria(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $evid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['criteriaId'];
            $cri = $row['criteria'];
            $pts = $row['percentage'];

            echo '<tr>';
            echo '<td>' . $id . '</td>';
            echo '<td>' . $cri . '</td>';
            echo '<td>' . $pts . '</td>';
            echo '<td>
                    <i class="fa-solid fa-pen-to-square edit-icon-cri" data-id="'.$id.'"  data-criteria="'.$cri.'" data-pts="'.$pts.'" onclick="openEditCriModal(this)" style="cursor: pointer;"></i>
                    <i class="fa-solid fa-trash-can delete-icon-cri" data-id="'.$id.'" style="cursor: pointer;"></i>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4">No criterias.</td></tr>';
    }
    $result->free();
    $stmt->close();
}
?>