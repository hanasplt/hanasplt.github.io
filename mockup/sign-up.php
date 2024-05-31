<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGNUP</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    if (isset($_POST['signup-button'])) {
        // database connection
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "chestechshopdb";

        // create connection
        $conn = mysqli_connect($servername, $db_username, $db_password, $dbname);

        // check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        session_start();
        $error = "";

        $first_name = $_POST['myFirst'];
        $last_name = $_POST['myLast'];
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $pass = $_POST['myPass'];
        $confirm = $_POST['confirm'];

        // Password validation
        $isValid = true;
        if ($pass !== $confirm) {
            echo "<script>Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Passwords do not match!'
            }).then(function() {
                window.location.href = 'sign-up.html';
            });</script>";
            $isValid = false;
        } elseif (strlen($pass) < 8) {
            echo "<script>Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Passwords must be 8 or more characters!'
            }).then(function() {
                window.location.href = 'sign-up.html';
            });</script>";
            $isValid = false;
        } 

        if ($isValid) {
            $sql_check = "SELECT * FROM client_accounts WHERE phone_number = '$phone' OR username = '$username'";
            $result_check = mysqli_query($conn, $sql_check);

            if (mysqli_num_rows($result_check) > 0) {
                echo "<script>Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Email, phone number, or username already exists.'
                }).then(function() {
                    window.location.href = 'sign-up.php';
                });</script>";
                exit;
            } else {
                $sql_insert = "INSERT INTO client_accounts (first_name, last_name, username, phone_number, email, pass)
                VALUES ('$first_name', '$last_name', '$username', '$phone', '$email', '$pass')";

                if (mysqli_query($conn, $sql_insert)) {
                 $_SESSION['username'] = $username;
                    echo "<script>Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Thank you for signing up. Welcome to CHES!'
                    }).then(function() {
                        window.location.href = 'cp-homepage.php';
                    });</script>";
                } else {
                    echo "Error: " . $sql_insert . "<br>" . mysqli_error($conn);
                }
            }
        }

        // close connection
        mysqli_close($conn);
    }
    ?>
</body>
</html>