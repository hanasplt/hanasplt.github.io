<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="assets/icons/logo-1.png" type="image/png">
    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container-2">
        <div class="left-part">
            <img src="assets/icons/banner-2.png" alt="Banner">
        </div>
        <div class="right-part">
            <div class="msg" id="msg"></div>
            <?php
                if (isset($_SESSION['error'])) {
                    echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
                    unset($_SESSION['error']);
                }
            ?>
            <h1>Welcome.</h1>
            <p>Log in your credentials to continue.</p>
            <form class="login" id="loginform" action="process.php" method="post">
                <label for="email">Email</label>
                <input type="text" id="email" name="email">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <div class="show-password" style="display: flex; justify-content: space-between;">
                    <span>
                        <input type="checkbox" id="show-password">
                        <label for="show-password">Show Password</label>
                    </span>
                    
                    <a href="resetpass.php"><u>Forgot Password?</u></a>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
            <p class="spectator">A <span><a href="spectator.php">Spectator</a></span>? No need to log in! Simply track the current rankings and scores by tapping on 'Spectator'. Let's enjoy the action together!</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            var passwordField = document.getElementById('password');
            if (this.checked) {
                passwordField.type = 'text'; // when checkbox is clicked, input will be shown
            } else {
                passwordField.type = 'password'; // when not, input is shown as dots
            }
        });

        document.getElementById('loginform').addEventListener('submit', function(event) {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var msg = document.getElementById('msg');
            msg.innerHTML = '';

            if (!username || !password) { // error message for login validation
                event.preventDefault();
                if (!username) {
                    msg.innerHTML += '<p style="color: red">Please enter your username.</p>';
                }
                if (!password) {
                    msg.innerHTML += '<p style="color: red">Please enter your password.</p>';
                }
            }
        });

        var msg = "<?= $_SESSION['status'] ?? ''; ?>";

        if(msg != '') {
            Swal.fire({
                title: "Email Sent!",
                text: msg,
                icon: "success"
            });
            <?php unset($_SESSION['status']); ?>
        }
    </script>

</body>
</html>
