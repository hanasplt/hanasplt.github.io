<?php
    require_once '../config/sessionConfig.php';
    require_once '../config/encryption.php';
    $conn = require_once '../config/db.php';

    

    if (isset($_POST['login'])) {
        $uzr = $_POST['email']; // user input--username (this is email)
        $paz = $_POST['password']; // user input--password

        if (empty($uzr) && empty($paz)) { // empty field, no input
            $_SESSION['error'] = 'Enter your email and password!';
            header('Location: ../public/login.php');
            exit();
        }

        $acc = array();
        $sql = "CALL sp_getAllAcc()"; // retrieving all accounts
        if ($retval = mysqli_query($conn, $sql)) {
            if (mysqli_num_rows($retval) > 0) {
                while ($row = mysqli_fetch_assoc($retval)) {
                    $usrid = $row['userId'];
                    $email = $row['email'];
                    $pass = decrypt($row['password'], $encryption_key);
                    $type = $row['type'];

                    $acc[] = array("email" => $email, "password" => $pass, 
                            "type" => $type, "rilId" => $usrid); // putting in an array for conditioning
                }
            }
            mysqli_free_result($retval);
            while (mysqli_more_results($conn) && mysqli_next_result($conn)); 
        } else {
            echo "Error fetching accounts: " . $conn->error;
        }

        $found = false;
        foreach ($acc as $account) {
            if ($account['email'] == $uzr && $account['password'] == $paz) {

                $found = true; // if user input matches a credential

                if ($account['type'] == "Committee") {
                    // Insert into logs. This user logged in.
                    $insLog = "INSERT INTO adminlogs
                                VALUES (NULL, NOW(), $account[rilId], 'Logged in.')";
                    $stmt = $conn->prepare($insLog);
                    $stmt->execute();

                    $_SESSION['role'] = 'Committee';

                    $_SESSION['loggedin'] = true;
                    header('Location: ../src/roles/committee/committee.php?id='.$account['rilId']); // sent to committee's ui
                    exit;
                } 
                
                else if ($account['type'] == "Judge") {
                    // Insert into logs. This user logged in.
                    $insLog = "INSERT INTO adminlogs
                                VALUES (NULL, NOW(), $account[rilId], 'Logged in.')";
                    $stmt = $conn->prepare($insLog);
                    $stmt->execute();

                    $_SESSION['role'] = 'Judge';

                    $_SESSION['loggedin'] = true;
                    header('Location: ../src/roles/judge/judge.php?id='.$account['rilId']); // sent to judges' ui
                    exit;
                } 
                
                else {
                    // Insert into logs. This user logged in.
                    $insLog = "INSERT INTO adminlogs
                                VALUES (NULL, NOW(), $account[rilId], 'Logged in.')";
                    $stmt = $conn->prepare($insLog);
                    $stmt->execute();

                    $_SESSION['role'] = 'Admin';

                    $_SESSION['loggedin'] = true;
                    header('Location: ../src/roles/admin/admin.php?id='.$account['rilId']); // sent to admin page
                    exit;
                }
                
            }
        }
        
        $_SESSION['error'] = 'Wrong credential!';
        header('Location: ../public/login.php'); // no credential matches! sent to login page
        exit();
    }

    header('Content-Type: text/html');

    if (isset($_POST['changepass'])) { // mandatory change password (judge) 
        $id = $_POST['jid'];
        $newpass = encrypt($_POST['newpass'], $encryption_key);
        $logstat = "finish";

        $sql = "CALL sp_editAccPass('$id', '$newpass', '$logstat')"; // user password updated
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Password Changed Successfully!'); window.location.href = 'judge.php?id=$id'; </script>";
        } else {
        }
    }

    if (isset($_POST['changepassfaci'])) { // mandatory change password (committee)
        $id = $_POST['fid'];
        $newpass = encrypt($_POST['newpass'], $encryption_key);
        $logstat = "finish";

        $sql = "CALL sp_editAccPass('$id', '$newpass', '$logstat')"; // user password updated
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Password Changed Successfully!'); window.location.href = 'committee.php?id=$id'; </script>";
        } else {
        }
    }

    $conn->close();
?>