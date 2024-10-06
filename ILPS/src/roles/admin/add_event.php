<?php
include '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day_id = $_POST['day_id'];
    $time = $_POST['time'];
    $type = $_POST['type'];
    $activity = $_POST['activity'];
    $location = $_POST['location'];
    $gameNo = ($type === 'sports') ? $_POST['game_number'] : null; // Set to null if not sports
    $teamA = ($type === 'sports') ? $_POST['teamA'] : null; // Set to null if not sports
    $teamB = ($type === 'sports') ? $_POST['teamB'] : null; // Set to null if not sports
    $status = $_POST['status'];

    if (empty($day_id) || empty($time) || empty($activity) || empty($location) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $team_names = [
        'teamA_name' => '',
        'teamB_name' => '',
    ];

    if (empty($teamA)) {
        $teamA = null;
    }
    if (empty($teamB)) {
        $teamB = null;
    }

    $query = "INSERT INTO scheduled_eventstoday (day_id, time, type, activity, location, gameNo, teamA, teamB, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    $stmt->bind_param("issssssss", $day_id, $time, $type, $activity, $location, $gameNo, $teamA, $teamB, $status);

    if ($stmt->execute()) {
        // Fetch team A name
        if ($teamA !== null) {
            $query_teamA = "SELECT teamName FROM vw_teams WHERE teamId = ?";
            $stmt_teamA = $conn->prepare($query_teamA);
            $stmt_teamA->bind_param("i", $teamA);
            $stmt_teamA->execute();
            $result_teamA = $stmt_teamA->get_result();
            if ($row = $result_teamA->fetch_assoc()) {
                $team_names['teamA_name'] = $row['teamName'];
            }
            $stmt_teamA->close();
        }

        // Fetch team B name
        if ($teamB !== null) {
            $query_teamB = "SELECT teamName FROM vw_teams WHERE teamId = ?";
            $stmt_teamB = $conn->prepare($query_teamB);
            $stmt_teamB->bind_param("i", $teamB);
            $stmt_teamB->execute();
            $result_teamB = $stmt_teamB->get_result();
            if ($row = $result_teamB->fetch_assoc()) {
                $team_names['teamB_name'] = $row['teamName'];
            }
            $stmt_teamB->close();
        }

        echo json_encode([
            'success' => true,
            'message' => 'Event added successfully.',
            'teamA_name' => $team_names['teamA_name'],
            'teamB_name' => $team_names['teamB_name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add event.']);
    }

    $stmt->close();
    $conn->close();
}
