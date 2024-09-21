<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../resources/PHPMailer/src/Exception.php';
require '../../resources/PHPMailer/src/PHPMailer.php';
require '../../resources/PHPMailer/src/SMTP.php';

$conn = include '../../config/db.php';
require_once '../../config/sessionConfig.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_POST['email'];
$token = bin2hex(random_bytes(16));
$expiry = "";

$sql = "SELECT userId
        FROM accounts
        WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result -> num_rows > 0) {
    $sql = "UPDATE accounts
            SET reset_token = ?,
                reset_token_expiration = NOW() + INTERVAL 10 MINUTE
            WHERE email = ?";
    
    $stmt = $conn -> prepare($sql);
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'sawatdeka10@gmail.com';                //SMTP username
        $mail->Password   = 'fbsguftwlpwbupxu';                     //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('sawatdeka10@gmail.com', 'Intramural Leaderboard Point System - Reset Password');
        $mail->addAddress($email);               //Name is optional

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Password Reset Code';
        $mail->Body = '<div style="font-family: Poppins, sans-serif; padding: 20px; max-width: 600px; margin: auto;">
                            <div style="text-align: center;">
                                <img src="../public/assets/icons/logo.png" alt="Logo" style="width: 100px; height: auto; margin-bottom: -15px;">
                                <h2>Password Reset Request</h2>
                            </div>
                            <div style="background-color: #ffffff; padding: 20px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
                                <p style="font-weight: 700;">Hi there!</p>
                                <p>We received a request to reset your password. Click the button below to proceed. <b>This link will expire in 10 minutes.</b></p>
                                <div style="text-align: center; margin-top: 40px; margin-bottom: 40px;">
                                    <a href="http://localhost:3000/aims%20-%20Copy/forgotpass/proceed-reset-pass.php?token=' . $token . '" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;">Reset Your Password</a>
                                </div>
                                <hr>
                                <p style="font-size: small; color: gray;">If you did not request this, please ignore this email.</p>
                            </div>
                            <p style="font-size: x-small; color: gray; text-align: center;">© 2024 Intramural Leaderboard and Points System. All rights reserved.</p>
                        </div>
                        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">';


        if($mail->send()) {
            $_SESSION['status'] = "Thank you! Please check your email.";
            header("Location: ../../public/login.php");
            exit;
        }
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    $_SESSION['status'] = "Invalid/No email found.";
    header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit;
}

$conn->close();

?>