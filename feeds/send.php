<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Database connection
$connect = new mysqli("localhost", "root", "", "bsit_feedback_system");

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

if (isset($_POST['send'])) {

    $student_id = $_POST['student_id'];
    $email = $_POST['email'];

    // ✅ Generate OTP (SERVER SIDE)
    $otp = rand(100000, 999999);

    // ✅ Get IP
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // ✅ Store session (IMPORTANT)
    $_SESSION['reset_student_id'] = $student_id;
    $_SESSION['reset_email'] = $email;

    // ✅ Insert OTP into database
    $stmt = $connect->prepare("INSERT INTO otp (student_id, email, otp_send, ip) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $student_id, $email, $otp, $ip_address);

    if ($stmt->execute()) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'elychristian.bucasas@evsu.edu.ph';
            $mail->Password = 'qpydnbiyqitzeqgi'; // ✅ your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('elychristian.bucasas@evsu.edu.ph', 'EVSU System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "OTP Verification";
            $mail->Body = "Your OTP is: <b>$otp</b>";

            $mail->send();

            // ✅ REDIRECT BACK TO forgot_password.php (NOT verify.php)
            echo "
            <script>
                alert('OTP sent successfully!');
                window.location.href='forgot_password.php';
            </script>";

        } catch (Exception $e) {
            echo "
            <script>
                alert('Mailer Error: {$mail->ErrorInfo}');
                window.location.href='forgot_password.php';
            </script>";
        }

    } else {
        echo "
        <script>
            alert('Database error: {$connect->error}');
            window.location.href='forgot_password.php';
        </script>";
    }
}
?>