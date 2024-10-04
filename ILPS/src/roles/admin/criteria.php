<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
  
    require_once '../../../config/sessionConfig.php'; // Session Cookie
    require_once '../../../config/encryption.php';
    $conn = require_once '../../../config/db.php'; // Database connection
    require_once '../admin/verifyLoginSession.php'; // Logged in or not
  
    $accId = $_SESSION['userId'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/criteria.css">

    <!--alert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Document</title>
</head>
<body>
    <div class="criteria-container">
        <form action="" id="addCriForm">
            <input type="hidden" name="action" value="addCriteria">
            <h2 class="addevent">Add Criteria</h2>

            <div id="msg">
                <!--display message here-->
            </div>
            <div class="criteria-form-group">
                <label for="eventIdC" class="form-label fw-bold">Event Name:</label>
                <select id="eventIdC" name="eventIdC" class="form-select" required>
                    <?php
                        $sql = "CALL sp_getEventFrom(?);";
                        $ev_type = "Socio-Cultural";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $ev_type);
                        $stmt->execute();
                        $result = $stmt->get_result();
                                    
                        if($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $db_evval = $row['eventID'];
                                $db_evname = $row['eventName'];
                                $db_evType = $row['eventType'];

                                ?>
                                <option value="<?php echo $db_evval; ?>" data-type="<?php echo $db_evType; ?>">
                                    <?php echo $db_evname; ?>
                                </option>
                                <?php
                            }
                        } else {
                            echo '<option selected disabled value=0>No Event/s exists.</option>';
                        }
                        $result->free();
                        $stmt->close();
                    ?>
                </select>
                <input type="text" id="eventname" name="eventname" hidden>
            </div>
            <div class="items">
                <div class="row">
                    <div class="col-7">
                        <label for="" class="form-label fw-bold">Criteria</label>
                    </div>
                    <div class="col-3">
                        <label for="" class="form-label fw-bold">Percentage</label>
                    </div>
                </div>
                <div class="row data-row">
                    <div class="col-7">
                        <input type="text" class="form-control" name="criteria[]" required>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control criPts" name="criPts[]" required>
                    </div>
                    <div class="col-1 add-btn">
                        <button type="button" id="addMore" class="btn" title="Add more row" style="background-color: #45a049; color: white;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" id="criteria-cancelBtn">Cancel</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/eventCriteria.js"></script>
</body>
</html>