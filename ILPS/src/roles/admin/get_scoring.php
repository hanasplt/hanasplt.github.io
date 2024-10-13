<?php

require_once '../../../config/sessionConfig.php'; // session Cookie
require_once '../../../config/db.php';
require_once 'adminPermissions.php'; // Retrieves admin permissions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve event scoring from the database
    $sql = "CALL sp_getScoring";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = []; // Initiliazie an array to store the data

    while ($row = $result->fetch_assoc()) {
        $num = $row['rankNo'];
        $rank = $row['rank'];
        $category = $row['eventCategory'];
        $points = $row['points'];
        
        // Two-dimensional array to achieve the tabl format
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

            // Display delete-icon - permitted to delete
            if (in_array('scoring_delete', $admin_rights)) {
            echo '<td><i class="fa-solid fa-trash-can delete-icon-pts" data-rank="'.$rank.'" data-rank-name="'.$rankname.'" style="cursor: pointer;"></i></td>';
            } else { // Display message - not permitted to delete
                echo '<td style="color: darkgrey;">Feature denied.</td>';
            }
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">No Event Scoring.</td></tr>';
    }

    $result->free();
    $stmt->close();
}


?>
