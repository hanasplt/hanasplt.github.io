<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ILPS | Reset your password </title>
    <!--Web-logo-->
    <link rel="icon" href="../assets/icons/logo-1.png">
    
    <!-- font --> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    
    <!-- css --> 
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo"><img src="../assets/icons/logo.png" alt="Logo"></div>
        <nav>
            <a href="../login.php" class="login-btn">Return to Login Page</a>
        </nav>
    </header>
    <div class="container">
        <div class="reset-pass">
            <h1>Reset your password</h1>
            <p>Please enter your email address to reset your password.</p>
            <form action="reset-pass.php" method="post">
                <input type="email" id="email" name="email" placeholder="Enter Email Address">
                <button> Reset Password </button>
            </form>
        </div>
        <div class="image-section">
            <img src="../assets/icons/banner.png" alt="Trophies">
        </div>
    </div>
    <footer>
        <div class="footer-left">
            <h6>Intramural Leaderboard and Points System</h6>
            <p>Transform intramurals with our Leaderboard & Points System. Real-time updates, competitive environment, community engagement. Streamline organization, identify talent effortlessly. Elevate your intramural experience today!</p>
        </div>
        <div class="footer-right">
            <h6>CONTACT US</h6>
            <p class="footer-email"><img src="../assets/icons/contact-email.png" alt="Email">john.doe@example.com</p>
            <p class="footer-contact"><img src="../assets/icons/contact-num.png" alt="Phone">(555) 123-4567</p>
            <p class="footer-add">123 Street Barangay Apokon, Tagum City, Davao Del Norte</p>
        </div>
    </footer>
    <div class="footer-footer">
        <hr style="height:1px; border-width:0; color: #60A85A; background-color:#60A85A">
        <p>© 2024 Dreamy Inc. All Rights Reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var msg = "<?= $_SESSION['status'] ?? ''; ?>";

        if(msg != '') {
            Swal.fire({
                title: "Oops..",
                text: msg,
                icon: "error"
            });
            <?php unset($_SESSION['status']); ?>
        }
    </script>
</body>
</html>
