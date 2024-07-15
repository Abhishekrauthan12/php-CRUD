<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'facebook');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send_otp'])) {
        $email            = $_POST['email'];
        $new_password     = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "Passwords do not match.";
        } else {
            // Generate a 6-digit OTP
            $otp = rand(100000, 999999);

            // Store the OTP and email in the session
            $_SESSION['otp']          = $otp;
            $_SESSION['email']        = $email;
            $_SESSION['new_password'] = $new_password;

            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'sonusinghstake@gmail.com';
                $mail->Password   = 'lfkc hjep blml ahls';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                //Recipients
                $mail->setFrom('sonusinghstake@gmail.com','Your Name');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = "Your OTP code is: $otp";

                $mail->send();
                $message = "OTP sent to $email";
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        $entered_otp = $_POST['otp'];

        // Check if the entered OTP matches the stored OTP
        if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
            // Update the user's password in the database
            $email = $_SESSION['email'];
            $new_password = $_SESSION['new_password'];

            $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ss", $new_password, $email);
            if ($stmt->execute()) {
                $message = "Password updated successfully.";
                unset($_SESSION['otp'], $_SESSION['email'], $_SESSION['new_password']);
            } else {
                $message = "Error updating password.";
            }
            $stmt->close();
        } else {
            $message = "Invalid OTP. Please try again.";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New password</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <h2>facebook</h2>
    <div class="container">
        <h3>create new password</h3><hr>
        <form action="password.php" method="POST">
        <?php if (!isset($_SESSION['otp'])): ?>
        <input type="email" id="email" name="email" placeholder="Enter Email id" required><br><br>
    <input type="password" id="password" name="new_password" placeholder="Set new password" required><br><br>
    <input type="password" id="password" name="confirm_password" placeholder="Confirm password" required><br>
    <input type="submit" name="send_otp" value="Send OTP"><br><br>
    <?php else: ?>
   <input type="otp" name="otp" placeholder="Enter OTP">
   <input type="submit" name="verify_otp" value="verify OTP"><br>
   <p class="textarea">By clicking you agree to our <a href=""> Terms&condition</a> and <a href="">Privacy Policy.</a></p>
   <?php endif; ?>
        </form>
    </div>
    <p><?php echo $message; ?></p>
</body>
</html>