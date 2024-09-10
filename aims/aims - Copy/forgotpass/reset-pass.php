<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$conn = include 'db.php';

$email = $_POST['email'];
$token = bin2hex(random_bytes(16));
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

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
                reset_toke_expiration = ?
            WHERE email = ?";
    
    $stmt = $conn -> prepare($sql);
    $stmt->bind_param("sss", $token, $expiry, $email);
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
        $mail->Body    = '<h2>DO NOT REPLY TO THIS.</h2>
                            Click <a href="http://localhost:3000/forgotpass/proceed-reset-pass.php?token='.$token.'">here</a> 
                            to reset your password :) ';


        if($mail->send()) {
            $_SESSION['status'] = "Thank you! Please check your email.";
            header("Location: login.php");
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