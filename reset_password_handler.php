<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires > NOW()");
    if (!$stmt) {
        echo "error: " . $conn->error;
        exit;
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Update user's password in the users table
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        if (!$stmt) {
            echo "error: " . $conn->error;
            exit;
        }
        $stmt->bind_param("ss", $password, $email);
        if (!$stmt->execute()) {
            echo "error: " . $stmt->error;
            exit;
        }

        // Delete the used token
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        if (!$stmt) {
            echo "error: " . $conn->error;
            exit;
        }
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "success";
    } else {
        echo "invalid_token";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>