<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = $_POST['otp'];

    if (!isset($_SESSION['registration_data'])) {
        echo "session_expired";
        exit;
    }

    $storedOtp = $_SESSION['registration_data']['otp'];

    if ($enteredOtp == $storedOtp) {
        $data = $_SESSION['registration_data'];

        $stmt = $conn->prepare("INSERT INTO users (name, email, mobile, aadhaar, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['name'], $data['email'], $data['mobile'], $data['aadhaar'], $data['password']);

        if ($stmt->execute()) {
            unset($_SESSION['registration_data']); // Clear session
            echo "success";
        } else {
            echo "db_error";
        }
    } else {
        echo "invalid_otp";
    }
}
?>
