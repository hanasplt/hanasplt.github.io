<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: sign-in.php");
    exit();
}

$username = $_SESSION['username'];
include 'db-connection.php';

$sql = "SELECT pass, profile_picture FROM client_accounts WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pass = htmlspecialchars($row['pass']);
    $client_img = htmlspecialchars($row['profile_picture']);
} else {
    echo "User not found!";
    exit();
}

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_pass']) && isset($_POST['confirm_pass'])) {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($new_pass === $confirm_pass) {
        $update_sql = "UPDATE client_accounts SET pass = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_pass, $username);
        if ($update_stmt->execute()) {
            $success = true;
        } else {
            echo "Error updating password: " . $conn->error;
            exit();
        }
    } else {
        echo "<script>alert('Passwords do not match!')</script>";
        echo "<script>window.location.href='change-password.php'</script>";
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>CHES Cellphone and Accessories Shop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--css-->
    <link href="change-password.css" type="text/css" rel="stylesheet" />
    <!---->

    <!--fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Krona+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
    <!---->

    <!--icons-->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <!---->

    <!--Web-logo-->
    <link rel="icon" href="icons/logo.svg">
    <!---->

    <!--sweet alert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!---->

</head>
<body>

<!--header-->
<div class="header">
    <div class="left-header">
        <i class="fi fi-rr-arrow-small-left" id="backButtonID"></i>
        <p>Change Password</p>
    </div>
    <div class="right-header">
        <img src="icons/logo.svg">
        <p>CHES</p>
    </div>
</div>
<!---->

<!--main container-->
<form method="post">
    <div class="main-container">
        <div class="left-main">
            <img src="icons/profile.png" <?php if ($client_img !== "") echo 'src="images/' . $client_img . '"'; ?> alt="Upload Image" class="pfp"><br><br>
            <button type="submit" class="save-changes" name="save">SAVE CHANGES</button><br>
            <button type="button" class="cancel-btn" id="cancelID">BACK</button>
        </div>
        <div class="vertical"></div>

        <div class="right-main">
            <p>Current Password</p>
            <input type="password" class="display-info" value="<?php echo $pass; ?>" id="currentPassID" readonly>
            <img src="icons/eye-close.svg" id="eyeIcon"><br>
            <p>New Password</p>
            <input type="password" class="display-info" placeholder="Enter a new password" id="newPassID" name="new_pass">
            <img src="icons/eye-close.svg" id="eyeIcon2"><br>
            <p>Confirm Password</p>
            <input type="password" class="display-info" placeholder="Retype your new password" name="confirm_pass" id="confirmPassID">
            <img src="icons/eye-close.svg" id="eyeIcon3"><br>
        </div>
    </div>
</form>

<!--script-->
<script type="text/javascript">
    //current pass
    let currentPass = document.querySelector('#currentPassID');
    let eyeIcon = document.querySelector('#eyeIcon');

    eyeIcon.onclick = () => {
        if(currentPass.type == "password") {
            currentPass.type = "text";
            eyeIcon.src = "icons/eye-open.svg";
        }
        else {
            currentPass.type = "password";
            eyeIcon.src = "icons/eye-close.svg";
        }
    }

    //new pass
    let newPass = document.querySelector('#newPassID');
    let eyeIcon2 = document.querySelector('#eyeIcon2');

    eyeIcon2.onclick = () => {
        if(newPass.type == "password") {
            newPass.type = "text";
            eyeIcon2.src = "icons/eye-open.svg";
        }
        else {
            newPass.type = "password";
            eyeIcon2.src = "icons/eye-close.svg";
        }
    }

    //confirm pass
    let confirmPass = document.querySelector('#confirmPassID');
    let eyeIcon3 = document.querySelector('#eyeIcon3');

    eyeIcon3.onclick = () => {
        if(confirmPass.type == "password") {
            confirmPass.type = "text";
            eyeIcon3.src = "icons/eye-open.svg";
        }
        else {
            confirmPass.type = "password";
            eyeIcon3.src = "icons/eye-close.svg";
        }
    }

    let backbutton = document.querySelector('#backButtonID');
    let cancelbutton = document.querySelector('#cancelID');
     backbutton.onclick = () => {
        window.location.href = 'my-profile.php';
    }
    cancelbutton.onclick = () => {
        window.location.href = 'edit-profile.php';
    }
</script>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $success): ?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Are you sure you want to change your password?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#F79256',
        cancelButtonColor: '#2A5181',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
                'Password Changed Successfully!',
                'Your password has been changed successfully.',
                'success'
            ).then(() => {
                window.location.href = 'my-profile.php';
            });
        }
    });
});
</script>
<?php endif; ?>


<!---->
</body>
</html>