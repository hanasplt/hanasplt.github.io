<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../resources/PHPMailer/src/Exception.php';
require '../../resources/PHPMailer/src/PHPMailer.php';
require '../../resources/PHPMailer/src/SMTP.php';

$conn = include '../../config/db.php';

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
        $mail->Body = '<h2>Password Reset Request</h2>
                <p>Hi there,</p>
                <p>We received a request to reset your password. Click the link below to proceed:</p>
                <p><a href="http://localhost:3000/aims%20-%20Copy/forgotpass/proceed-reset-pass.php?token=' . $token . '">Reset Your Password</a></p>
                <p><b>Note:</b> This link will expire in 10 minutes.</p>
                <p>If you did not request this, please ignore this email.</p>
                <p>Thank you!</p>
                <footer>
                    <p>Do not reply to this email.</p>
                    <p>Best regards,<br>Intramural Leaderboard Point System Team</p>
                </footer>';


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