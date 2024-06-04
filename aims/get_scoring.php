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
    $sql = "CALL sp_getScoring";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $num = $row['rankNo'];
        $rank = $row['rank'];
        $category = $row['eventCategory'];
        $points = $row['points'];
        $data[$num][] = [
            'rankname' => $rank,
            'category' => $category,
            'points' => $points
        ];
    }

    if (!empty($data)) {
        foreach ($data as $rank => $entries) {
            echo '<tr>';
            echo '<td>' . $rank . '</td>';
            
            $individualPoints = '';
            $teamPoints = '';
            $rankname = '';

            foreach ($entries as $entry) {
                $rankname = $entry['rankname'];
                if ($entry['category'] == "Individual/Dual") {
                    $individualPoints = $entry['points'];
                } elseif ($entry['category'] == "Team") {
                    $teamPoints = $entry['points'];
                }
            }

            echo '<td>' . $rankname . '</td>';
            echo '<td>' . $individualPoints . '</td>';
            echo '<td>' . $teamPoints . '</td>';
            echo '<td><i class="fa-solid fa-trash-can delete-icon-pts" data-rank="'.$rank.'" style="cursor: pointer;"></i></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">No Scoring.</td></tr>';
    }

    $result->free();
    $stmt->close();
}


?>
