<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = "chestechshopDB";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    printf("Connection failed: %s\n", mysqli_connect_error());
    exit();
}

session_start();
$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pass'];

    $sql = "SELECT * FROM client_accounts WHERE username = ? AND pass = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        $success = true;
    } else {
        $success = false;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>CHES Cellphone and Accessories Shop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link href="sign-in.css" type="text/css" rel="stylesheet" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-solid-straight/css/uicons-solid-straight.css'>

    <!-- Web logo -->
    <link rel="icon" href="icons/logo.svg">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="maindiv">
   <div class="leftdiv">
    <img class="logo" src="icons/logo.svg"/>
    <br>
    <p class="welcome">Welcome Back!</p>
    <p class="sign">Sign in to continue</p>
    <p class="dont">Don't have an account? <a href="sign-up.html">Sign up</a></p>
   </div>
   <div class="rightdiv">
    <p class="sign2"><b>Sign In</b></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="input-con">
            <input class="username" type="text" name="username" id="myUser" placeholder="Username" required>
            <img src="icons/username.svg" alt="Username" class="field-image">
        </div>
        <div class="password-con">
            <input class="password" type="password" name="pass" id="myPass" placeholder="Password" required>
            <img src="icons/password.svg" alt="Password" class="field-image">
            <img src="icons/eye-close.svg" alt="Key" id="eye-close" class="eye-image" onclick="togglePasswordVisibility()">
        </div>
        <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <button type="submit" class="button-signin">SIGN IN</button>
    </form>
   </div>
</div>

<script type="text/javascript" src="sign-in.js"></script>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($success): ?>
        Swal.fire({
            imageUrl: 'icons/logo1.png',
            imageAlt: 'Custom image', 
            title: 'Welcome to CHES!',
            text: 'Thanks for signing in.',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'cp-homepage.php';
        });
    <?php else: ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Incorrect username or password!'
        });
    <?php endif; ?>
});
</script>
<?php endif; ?>

</body>
</html>