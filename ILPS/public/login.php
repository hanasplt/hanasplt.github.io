<?php 

require_once '../config/sessionConfig.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // If already logged in, redirect the user based on their role
    $role = $_SESSION['role'];

    if ($role == "Committee") {
        header('Location: ../src/roles/committee/committee.php?id='.$_SESSION['userId']); // sent to committee's ui
        exit;
    } else if ($role == "Judge") {
        header('Location: ../src/roles/judge/judge.php?id='.$_SESSION['userId']); // sent to judges' ui
        exit;
    } else {
        header('Location: ../src/roles/admin/admin.php?id='.$_SESSION['userId']); // sent to admin page
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../public/assets/icons/logo-1.png" type="image/png">
    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="container-2">
        <div class="left-part">
            <img src="../public/assets/icons/banner-2.png" alt="Banner">
        </div>
        <div class="right-part">
            <div class="msg" id="msg"></div>
            <?php
                if (isset($_SESSION['error'])) { // For displaying error
                    echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
                    unset($_SESSION['error']);
                }
            ?>
            <h1>Welcome.</h1>
            <p>Log in your credentials to continue.</p>
            <form class="login" id="loginform" action="../public/process.php" method="post">
                <label for="email">Email</label>
                <input type="text" id="email" name="email">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <div class="show-password" style="display: flex; justify-content: space-between;">
                    <span>
                        <input type="checkbox" id="show-password">
                        <label for="show-password">Show Password</label>
                    </span>
                    
                    <a href="forgotpass/resetpass.php"><u>Forgot Password?</u></a>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
            <p class="spectator">A <span><a href="../public/spectator.php">Spectator</a></span>? No need to log in! Simply track the current rankings and scores by tapping on 'Spectator'. Let's enjoy the action together!</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/js/login.js"></script>

</body>
</html>
