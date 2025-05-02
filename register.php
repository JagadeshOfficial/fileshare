<?php
session_start();
include("db.php");

require 'vendor/autoload.php'; // Composer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST["name"];
    $email    = $_POST["email"];
    $mobile   = $_POST["mobile"];
    $aadhaar  = $_POST["aadhaar"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $otp      = rand(100000, 999999);

    $_SESSION['registration_data'] = [
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'aadhaar' => $aadhaar,
        'password' => $password,
        'otp' => $otp
    ];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jagadeswararaovana@gmail.com';
        $mail->Password = 'ttimmgumtntdwokt'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('jagadeswararaovana@gmail.com', 'Registration System');
        $mail->addAddress($email);
        $mail->Subject = "Your OTP Code";
        $mail->Body    = "Dear $name,\n\nYour OTP code is: $otp\n\nThank you!";

        $mail->send();
        echo "success";
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
