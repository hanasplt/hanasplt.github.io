<?php
    include 'encryption.php';

    $conn = include 'db.php';

    session_start();

    if (isset($_POST['login'])) {
        $uzr = $_POST['email']; // user input--username
        $paz = $_POST['password']; // user input--password

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
                    header('Location: committee.php?id='.$account['rilId']); // sent to committee's ui
                    exit;
                } else {
                    header('Location: judge.php?id='.$account['rilId']); // sent to judges' ui
                    exit;
                }
            }
        }

        if (!$found) { // no credential matches, then checks admin credentials
            $admin = array();
            $sql = "CALL sp_getAdmin()";

            if ($retval = mysqli_query($conn, $sql)) {
                if (mysqli_num_rows($retval) > 0) {
                    while ($row = mysqli_fetch_assoc($retval)) {
                        $username = decrypt($row['username'], $encryption_key);
                        $pass = decrypt($row['password'], $encryption_key);

                        $admin[] = array("username" => $username, "password" => $pass);
                    }
                }
                mysqli_free_result($retval); 
                while (mysqli_more_results($conn) && mysqli_next_result($conn)); 
            } else {
                echo "Error fetching admin accounts: " . $conn->error;
            }

            foreach ($admin as $account) {
                if ($account['username'] == $uzr && $account['password'] == $paz) {
                    header('Location: admin.php'); // sent to admin ui
                    exit;
                }
            }

            $_SESSION['error'] = 'Wrong credential!';
            header('Location: login.php'); // no credential matches! sent to login page
            exit();
        }
    }

    header('Content-Type: text/html');

    if (isset($_POST['changepass'])) { // mandatory change password 
        $id = $_POST['jid'];
        $newpass = encrypt($_POST['newpass'], $encryption_key);
        $logstat = "finish";

        $sql = "CALL sp_editAccPass('$id', '$newpass', '$logstat')"; // user password updated
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Password Changed Successfully!'); window.location.href = 'judge.php?id=$id'; </script>";
        } else {
        }
    }

    if (isset($_POST['changepassfaci'])) { // mandatory change password 
        $id = $_POST['fid'];
        $newpass = encrypt($_POST['newpass'], $encryption_key);
        $logstat = "finish";

        $sql = "CALL sp_editAccPass('$id', '$newpass', '$logstat')"; // user password updated
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Password Changed Successfully!'); window.location.href = 'committee.php?id=$id'; </script>";
        } else {
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
        $id = $_POST['id'];
        $newpass = encrypt($_POST['newpass'], $encryption_key);
        $token = "NULL";

        $sql = "CALL sp_resetAccPass($id, '$newpass', '$token')"; // user password reset
        if(mysqli_query($conn, $sql)) {
            // Return success response as JSON
            echo json_encode([
                'status' => 'success',
                'message' => 'Password Changed Successfully!'
            ]);
        } else {
            // Return error response as JSON
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $sql . "<br>" . $conn->error
            ]);
        }

        $stmt->close();
        exit;
    }

    $conn->close();
?>