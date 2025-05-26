<?php
session_start();
include("db.php");

require 'vendor/autoload.php'; // Same path as register.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if email exists in the users table
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    if (!$stmt) {
        echo "error: Database error - " . $conn->error;
        file_put_contents('db_error.txt', $conn->error . "\n", FILE_APPEND);
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name);
        $stmt->fetch();

        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store token in the password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        if (!$stmt) {
            echo "error: Database error - " . $conn->error;
            file_put_contents('db_error.txt', $conn->error . "\n", FILE_APPEND);
            exit;
        }
        $stmt->bind_param("sss", $email, $token, $expires);
        if (!$stmt->execute()) {
            echo "error: Database error - " . $stmt->error;
            file_put_contents('db_error.txt', $stmt->error . "\n", FILE_APPEND);
            exit;
        }

        // Send reset email using PHPMailer (matching register.php)
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jagadeswararaovana@gmail.com';
            $mail->Password = 'ttimmgumtntdwokt';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('jagadeswararaovana@gmail.com', 'FileSharePro');
            $mail->addAddress($email, $name);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Dear $name,\n\nClick the following link to reset your password: http://localhost/fileshare/reset_password.php?token=$token\nThis link will expire in 1 hour.";
            $mail->AltBody = "Dear $name,\n\nClick the following link to reset your password: http://localhost/fileshare/reset_password.php?token=$token\nThis link will expire in 1 hour.";

            $mail->send();
            echo "success";
        } catch (Exception $e) {
            echo "email_failed: " . $mail->ErrorInfo;
            file_put_contents('email_error.txt', $mail->ErrorInfo . "\n", FILE_APPEND);
        }
    } else {
        echo "user_not_found";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>