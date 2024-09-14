<?php
  include 'encryption.php';
  $conn = include 'db.php';

  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  //add event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $eventName = $_POST['eventName'];
    $eventType = $_POST['eventType'];
    $eventCategory = $_POST['eventCategory'];

    $sql = "CALL sp_insertEvent(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("isss", $eventId, $eventName, $eventType, $eventCategory);

    if ($stmt->execute()) {
        // Return success response as JSON
        echo json_encode([
            'status' => 'success',
            'message' => 'New event added successfully!'
        ]);
    } else {
        // Return error response as JSON
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $sql . "<br>" . $conn->error
        ]);
    }

    $stmt->close();
    exit;  // End script to ensure no further output
  }


  //add event contestant
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addContestant') {
    $team = $_POST['contestantId'];
    $name = $_POST['contestantName'];
    $event = $_POST['conEvId'];

    $sql = "CALL sp_getContestant(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $team, $event);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Contestant already exists!']);
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertEventContestant(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $team, $event);
    
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Contestant added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
        }
    }
    $stmt->close();
    exit;
  }

  //add event Committee
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addComt') {
    $comtid = $_POST['comtId'];
    $evid = $_POST['comtEvId'];

    $sql = "CALL sp_getComt(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $comtid, $evid);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Committee already exists!']);
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertEventComt(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $comtid, $evid);
    
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Committee added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => ' Error: ' . $sql . '<br>' . $conn->error]);
        }
    }
    $stmt->close();
    exit;
  }

  //add event judge
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addJudge') {
    $judgeid = $_POST['judgeId'];
    $evid = $_POST['judgeEvId'];

    $sql = "CALL sp_getJudge(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $judgeid, $evid);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Judge already exists!']);
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertEventJudge(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $judgeid, $evid);
    
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Judge added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Error: " . $sql . "<br>" . $conn->error]);
        }
    }
    $stmt->close();
    exit;
  }

  //add event criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addCriteria') {
    $event = $_POST['criEVId'];
    $criteria = $_POST['criteria'];
    $pts = $_POST['criPts'];

    $sql = "CALL sp_insertCriteria(?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $event, $criteria, $pts);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Criteria added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Error: " . $sql . "<br>" . $conn->error]);
    }
    $stmt->close();
    exit;
  }

  //edit criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editCriteria') {
    $criid = $_POST['editcriId'];
    $cri = $_POST['editcriteria'];
    $pts = $_POST['editcriPts'];
    
    $sql = "CALL sp_editCriteria(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $criid, $cri, $pts);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Criteria updated successfully!']);
    }
    $stmt->close();
    exit;
  }

  //add scoring
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addScoring') {
    $ranknum = $_POST['rankNo'];
    $name = $_POST['rankName'];
    $category = $_POST['scoringCategory'];
    $pts = $_POST['scorePts'];

    $sql = "CALL sp_getScoringChk(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $ranknum, $category);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Scoring already exists!']);
    } else {
        $retval->free();
        $stmt->close();

        $sql = "CALL sp_insertScoring(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $ranknum, $name, $category, $pts);
    
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Scoring added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Error: " . $sql . "<br>" . $conn->error]);
        }
    }
    $stmt->close();
    exit;
  }

  //edit event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editEvent') {
    $eventid = $_POST['editeventId'];
    $eventtype = $_POST['editeventType'];
    $eventname = $_POST['editeventName'];
    $eventcat = $_POST['editeventCategory'];
    
    $sql = "CALL sp_editEvent(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $eventid, $eventtype, $eventname, $eventcat);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event updated successfully!']);
    }
    $stmt->close();
    exit;
  }

  //deletes an event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eventid'])) {
    $eventID = $_POST['eventid'];
    
    $sql = "CALL sp_delEvent(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  //deletes a contestant
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contid'])) {
    $id = $_POST['contid'];
    
    $sql = "CALL sp_delEventContestant(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  //deletes a Committee
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comtid'])) {
    $comtID = $_POST['comtid'];
    
    $sql = "CALL sp_delEventComt(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comtID);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  //deletes a judge
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['judgeid'])) {
    $judgeID = $_POST['judgeid'];
    
    $sql = "CALL sp_delEventJudge(?)";  
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $judgeID);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  //deletes a criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['criid'])) {
    $criID = $_POST['criid'];
    
    $sql = "CALL sp_delCriteria(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $criID);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  //deletes a scoring
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rank'])) {
    $rank = $_POST['rank'];
    
    $sql = "CALL sp_delScoring(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rank);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $conn->error;
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }
?>