<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
  
    require_once '../../../config/sessionConfig.php'; // Session Cookie
    require_once '../../../config/encryption.php';
    $conn = require_once '../../../config/db.php'; // Database connection
    require_once '../admin/verifyLoginSession.php'; // Logged in or not
  
    $accId = $_SESSION['userId'];
    $event_id = '';

    if (isset($_GET['event']) && isset($_GET['eventname'])) {
        $event_id = $_GET['event'];
        $event_name = $_GET['eventname'];
    }
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
    <script>
        var get_eventid = <?php echo $event_id; ?>;
    </script>
    <div class="criteria-container">
        <form action="" id="editCriForm<?php echo $event_id; ?>">
            <input type="hidden" name="action" value="editCriteria">
            <input type="hidden" name="editeventIdC" value="<?php echo $event_id; ?>">
            <input type="hidden" name="editeventname" value="<?php echo $event_name; ?>">
            <h2 class="addevent">Edit Criteria for <?php echo $event_name; ?></h2>

            <div id="msg">
                <!--display message here-->
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
            <?php
                $getCriterias = "SELECT * FROM vw_criteria WHERE eventID = ?;";

                $stmt = $conn->prepare($getCriterias);
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $retval = $stmt->get_result();

                if ($retval->num_rows > 0) {
                    $numrow = 0; // To change button

                    // display data
                    while ($row = $retval->fetch_assoc()) {
                        $numrow++;
                        if ($numrow > 1) {
?>
                <div class="row data-row">
                    <div class="col-7">
                        <input type="text" class="form-control" name="editcriteria[]" value="<?php echo $row['criteria'] ?>" required>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control" id="editcriPts" name="editcriPts[]" value="<?php echo $row['percentage'] ?>" required>
                    </div>
                    <div class="col-1 remove-btn">
                        <button type="button" id="remove" class="btn btn-danger" title="Remove this row">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
<?php
                        } else {
?>
                <div class="row data-row">
                    <div class="col-7">
                        <input type="text" class="form-control" name="editcriteria[]" value="<?php echo $row['criteria'] ?>" required>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control" id="editcriPts" name="editcriPts[]" value="<?php echo $row['percentage'] ?>" required>
                    </div>
                    <div class="col-1 add-btn">
                        <button type="button" id="addMore" class="btn" title="Add more row" style="background-color: #45a049; color: white;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
<?php
                        }
                    }
                }
            ?>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" id="editcriteria-cancelBtn<?php echo $event_id; ?>">Cancel</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/editeventCriteria.js"></script>
</body>
</html>