<?php

include '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "SELECT teamId, teamName FROM teams WHERE status IS NULL OR status != '0'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $teams = [];
            while ($team = $result->fetch_assoc()) {
                $teams[] = $team;
            }

            echo json_encode(['success' => true, 'teams' => $teams]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No teams found']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching teams: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
