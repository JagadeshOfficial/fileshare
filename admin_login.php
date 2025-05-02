<?php
session_start();
include("db.php");



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the user from the database
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $user_id;
            echo "success"; // Send this to JS for redirect
        } else {
            echo "invalid_password";
        }
    } else {
        echo "user_not_found";
    }

    $stmt->close();
    $conn->close();
}
?>
