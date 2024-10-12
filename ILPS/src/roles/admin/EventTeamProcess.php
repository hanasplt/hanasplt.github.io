<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  require_once '../../../config/sessionConfig.php'; // Session Cookie
  require_once '../../../config/encryption.php';
  require_once '../../../config/db.php'; // Database connection
  require_once '../admin/verifyLoginSession.php'; // Logged in or not
  require_once 'adminPermissions.php'; // Retrieves admin permissions

  $accId = $_SESSION['userId'];

  //add event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $eventName = ucwords($_POST['eventName']);
    $eventType = $_POST['eventType'];
    $eventCategory = $_POST['eventCategory'];

    try {

        $sql = "CALL sp_insertEvent(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        $stmt->bind_param("isss", $eventId, $eventName, $eventType, $eventCategory);
    
        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Added event $eventName";
            $insertLogAct = "CALL sp_insertLog(?, ?)";
    
            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();
    
            // Return success response as JSON
            echo json_encode([
                'status' => 'success',
                'message' => 'New event added successfully!'
            ]);
        } else {
            // Return error response as JSON
            echo json_encode([
                'status' => 'error',
                'message' => 'Unable to insert event!'
            ]);
        }
    
        $stmt->close();
        exit;  // End script to ensure no further output

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
  }


  //add event contestant
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addContestant') {
    $team = $_POST['contestantId'];
    $name = $_POST['contestantName'];
    $event = $_POST['eventId'];
    $eventname = $_POST['selectedEventText'];
    $contNo = $_POST['contNum'] ?? NULL;

    if (empty($contNo)) {
        $contNo = NULL;
    }

    $sql = "CALL sp_getContestant(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $team, $event);
    $stmt->execute();
    $retval = $stmt->get_result();

    try {
        if ($retval->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Contestant already exists!']);
        } else {
            $retval->free();
            $stmt->close();
    
            $sql = "CALL sp_insertEventContestant(?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $team, $event, $contNo);
        
            if ($stmt->execute()) {
                // Insert in the logs
                $action = "Added contestant $name in the event $eventname";
                $insertLogAct = "CALL sp_insertLog(?, ?)";

                $stmt = $conn->prepare($insertLogAct);
                $stmt->bind_param("is", $accId, $action);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Contestant added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . ' ' . $conn->error]);
            }
        }
        $stmt->close();
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
  }

  //add event Committee
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addComt') {
    $comtid = $_POST['comtId'];
    $comtName = $_POST['comtName'];
    $evid = $_POST['eventIdComt'];
    $evname = $_POST['comtEVName'];

    try {

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
                // Insert in the logs
                $action = "Added event committee $comtName in the event $evname";
                $insertLogAct = "CALL sp_insertLog(?, ?)";
    
                $stmt = $conn->prepare($insertLogAct);
                $stmt->bind_param("is", $accId, $action);
                $stmt->execute();
    
                echo json_encode(['status' => 'success', 'message' => 'Committee added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => ' Error: ' . $sql . ' ' . $conn->error]);
            }
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
  }

  //add event judge
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addJudge') {
    $judgeid = $_POST['judgeId'];
    $evid = $_POST['eventIdJ'];
    $event = $_POST['judgeEVName'];
    $judge = $_POST['judgeName'];

    $sql = "CALL sp_getJudge(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $judgeid, $evid);
    $stmt->execute();
    $retval = $stmt->get_result();

    try {
        
        if ($retval->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Judge already exists!']);
        } else {
            $retval->free();
            $stmt->close();
    
            $sql = "CALL sp_insertEventJudge(?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $judgeid, $evid);
        
            if ($stmt->execute()) {
                // Insert in the logs
                $action = "Added event judge $judge in the event $event";
                $insertLogAct = "CALL sp_insertLog(?, ?)";
    
                $stmt = $conn->prepare($insertLogAct);
                $stmt->bind_param("is", $accId, $action);
                $stmt->execute();
    
                echo json_encode(['status' => 'success', 'message' => 'Judge added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Error: " . $sql . " " . $conn->error]);
            }
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
    
  }

  //add event criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addCriteria') {
    $event = $_POST['eventIdC'];
    $eventname = $_POST['eventname'];
    $criteria = $_POST['criteria'];
    $pts = $_POST['criPts'];
    $totalPts  = 0;

    try {
        // Checks whether a criteria exists for this event
        $sql = "CALL sp_getCriteria(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event);
        $stmt->execute();
        $retSum = $stmt->get_result();

        if ($retSum->num_rows > 0) {
            echo "There is already a Criteria for this event. Can't add anymore";
            exit;
        }

        $retSum->free();
        $stmt->close();
    
        // For loop to check total points
        for ($i = 0; $i < count($criteria); $i++) {
            $totalPts += $pts[$i];
        }

        if ($totalPts == 100) { // Proceed insertion
            for ($i = 0; $i < count($criteria); $i++) {
                // Insert criterias
                $sql = "CALL sp_insertCriteria(?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $event, $criteria[$i], $pts[$i]);
                $stmt->execute();
            }
        } else { // Total Points of Criteria must be 100
            echo "Please enter a value that ensures the total percentage is 100%";
            exit;
        }

        // Insert in the logs
        $action = "Added criteria/s for the event $eventname";
        $insertLogAct = "CALL sp_insertLog(?, ?)";

        $stmt = $conn->prepare($insertLogAct);
        $stmt->bind_param("is", $accId, $action);
        $stmt->execute();
        
        echo "success";
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }
  }

  //edit criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editCriteria') {
    $event = $_POST['editeventIdC'];
    $eventname = $_POST['editeventname'];
    $criteria = $_POST['editcriteria'];
    $pts = $_POST['editcriPts'];
    $totalPts  = 0;

    try {
        // For loop to check total points
        for ($i = 0; $i < count($criteria); $i++) {
            $totalPts += $pts[$i];
        }

        if ($totalPts == 100) { // Proceed insertion
            $delCriteria = "CALL sp_delCriteria(?)";

            $stmt = $conn->prepare($delCriteria);
            $stmt->bind_param("i", $event);
            $stmt->execute();

            $stmt->close();

            for ($i = 0; $i < count($criteria); $i++) {
                // Update criterias
                $sql = "CALL sp_insertCriteria(?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $event, $criteria[$i], $pts[$i]);
                $stmt->execute();
            }
        } else { // Total Points of Criteria must be 100
            echo "Please enter a value that ensures the total percentage is 100%";
            exit;
        }

        // Insert in the logs
        $action = "Updated criteria/s for the event $eventname";
        $insertLogAct = "CALL sp_insertLog(?, ?)";

        $stmt = $conn->prepare($insertLogAct);
        $stmt->bind_param("is", $accId, $action);
        $stmt->execute();
        
        echo "success";
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }
  }

  //add scoring
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'addScoring') {
    $ranknum = $_POST['rankNo'];
    $name = $_POST['rankName'];
    $category = $_POST['scoringCategory'];
    $pts = $_POST['scorePts'];

    try {
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
                // Insert in the logs
                $action = "Added event scoring rank $ranknum-$name($pts pts.) in the category $category";
                $insertLogAct = "CALL sp_insertLog(?, ?)";

                $stmt = $conn->prepare($insertLogAct);
                $stmt->bind_param("is", $accId, $action);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Scoring added successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Error: " . $sql . "<br>" . $conn->error]);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    $stmt->close();
    exit;
  }

  //edit event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'editEvent') {
    $eventid = $_POST['editeventId'];
    $eventtype = $_POST['editeventType'];
    $eventname = ucwords($_POST['editeventName']);
    $eventcat = $_POST['editeventCategory'];

    try {

        $sql = "CALL sp_editEvent(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $eventid, $eventtype, $eventname, $eventcat);

        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Updated event $eventname";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Event updated successfully!']);
        }
        $stmt->close();
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
    
  }

  // Retrieve team to edit
  if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['editID'])) {
    $eventid = $_GET['editID'];
  
    try {
      // Retrieve elimination in the database
      $getElim = "CALL sp_getEvent(?)";
  
      $stmt = $conn->prepare($getElim);
      $stmt->bind_param("i", $eventid);
      $stmt->execute();
  
      $retval = $stmt->get_result();
  
      $row = $retval->fetch_assoc();
      $courses = $row['eventElimination'];
  
      echo json_encode(['course' => $courses]);
  
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
  }

  //deletes an event
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eventid']) && isset($_POST['eventname'])) {
    $eventID = $_POST['eventid'];
    $eventName = $_POST['eventname'];
    
    $sql = "CALL sp_delEvent(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);

    $response = array();

    try {
        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Deleted event $eventName";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $conn->error;
        }

        $stmt->close();
        header('Content-Type: application/json');
        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }
  }

  //deletes a contestant
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contid'])) {
    $id = $_POST['contid'];
    $eventName = $_POST['eventname'];
    $contname = $_POST['contname'];
    
    $sql = "CALL sp_delEventContestant(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    $response = array();

    try {

        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Deleted contestant ($contname) in the event $eventName";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $conn->error;
        }

        $stmt->close();
        header('Content-Type: application/json');
        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }

  }

  //deletes a Committee
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comtid'])) {
    $comtID = $_POST['comtid'];
    $comtname = $_POST['name'];
    $event = $_POST['eventname'];
    
    $sql = "CALL sp_delEventComt(?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comtID);

    $response = array();

    try  {

        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Deleted committee $comtname in the event $event";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $conn->error;
        }

        $stmt->close();
        header('Content-Type: application/json');
        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }

  }

  //deletes a judge
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['judgeid'])) {
    $judgeID = $_POST['judgeid'];
    $name = $_POST['name'];
    $event = $_POST['eventname'];
    
    $sql = "CALL sp_delEventJudge(?)";  
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $judgeID);

    $response = array();

    try {
        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Deleted judge $name in the event $event";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $conn->error;
        }

        $stmt->close();
        header('Content-Type: application/json');
        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error:'. $e->getMessage()]);
    }

  }

  //deletes criteria
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['criteriaeventid'])) {
    $eventid = $_POST['criteriaeventid'];
    $event = $_POST['eventname'];
    
    try {
        $sql = "CALL sp_delCriteria(?)"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $eventid);

        $response = array();

        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Deleted the criteria/s in the event $event";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $conn->error;
        }

    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  //deletes a scoring
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rank'])) {
    $rank = $_POST['rank'];
    $rankname = $_POST['rankname'];
    
    try {
        $sql = "CALL sp_delScoring(?)"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rank);

        $response = array();

        if ($stmt->execute()) {
            // Insert in the logs
            $action = "Deleted the event scoring rank $rank-$rankname";
            $insertLogAct = "CALL sp_insertLog(?, ?)";

            $stmt = $conn->prepare($insertLogAct);
            $stmt->bind_param("is", $accId, $action);
            $stmt->execute();

            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['error'] = $conn->error;
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
    }
    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
  }
?>