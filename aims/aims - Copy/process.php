<?php
include 'encryption.php';

$servername = "localhost"; 
$username = "root"; 
$password = "";
$dbname = "ilps";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (isset($_POST['login'])) {
    $uzr = $_POST['username'];
    $paz = $_POST['password'];

    $acc = array();
    $sql = "CALL sp_getAllAcc()";
    if ($retval = mysqli_query($conn, $sql)) {
        if (mysqli_num_rows($retval) > 0) {
            while ($row = mysqli_fetch_assoc($retval)) {
                $usrid = $row['userId'];
                $username = decrypt($row['userId'], $encryption_key);
                $pass = decrypt($row['password'], $encryption_key);
                $type = $row['type'];

                $acc[] = array("username" => $username, "password" => $pass, "type" => $type, "rilId" => $usrid);
            }
        }
        mysqli_free_result($retval);
        while (mysqli_more_results($conn) && mysqli_next_result($conn)); 
    } else {
        echo "Error fetching accounts: " . $conn->error;
    }

    $found = false;
    foreach ($acc as $account) {
        if ($account['username'] == $uzr && $account['password'] == $paz) {
            $found = true;
            if ($account['type'] == "Facilitator") {
                header('Location: facilitator.php?id='.$account['rilId']);
                exit;
            } else {
                header('Location: judge.php?id='.$account['rilId']);
                exit;
            }
        }
    }

    if (!$found) {
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
                header('Location: admin.php');
                exit;
            }
        }

        $_SESSION['error'] = 'Wrong credential!';
        header('Location: login.php');
        exit();
    }
}

header('Content-Type: text/html');

if (isset($_POST['changepass'])) {
    $id = $_POST['jid'];
    $newpass = encrypt($_POST['newpass'], $encryption_key);
    $logstat = "finish";

    $sql = "CALL sp_editAccPass('$id', '$newpass', '$logstat')";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Password Changed Successfully!'); window.location.href = 'judge.php?id=$id'; </script>";
    } else {
    }
}

if (isset($_POST['changepassfaci'])) {
    $id = $_POST['fid'];
    $newpass = encrypt($_POST['newpass'], $encryption_key);
    $logstat = "finish";

    $sql = "CALL sp_editAccPass('$id', '$newpass', '$logstat')";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Password Changed Successfully!'); window.location.href = 'facilitator.php?id=$id'; </script>";
    } else {
    }
}

$conn->close();
?>