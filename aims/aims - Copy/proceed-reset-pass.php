<?php
session_start();

$conn = include 'db.php';

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT userId FROM accounts WHERE reset_token = ?";

    $stmt = $conn -> prepare($sql);
    $stmt -> bind_param("s", $token);
    $stmt -> execute();
    $result = $stmt->get_result();

    if($result -> num_rows > 0) {
        //fetch ang userId para ma update ang password
        // update token to null
        // create form for change password
    }
}

// update new password here {}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <form action="" method="post">
        <input type="hidden" name="userid" id="userid">
        <input type="password">
        <input type="password">
        <button>Change</button>
    </form>
</body>
</html>