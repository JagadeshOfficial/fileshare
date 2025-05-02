<?php
session_start();
include("db.php");  // Ensure your db.php file has the correct database connection setup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize the email input to prevent SQL injection
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Bind email parameter
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $user_email, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables upon successful login
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $user_email;

            // Send success response to frontend
            echo "success";
        } else {
            // Incorrect password, return error
            echo "invalid_password";
        }
    } else {
        // User not found, return error
        echo "user_not_found";
    }

    $stmt->close();
    $conn->close();
} else {
    // Handle other request methods, if necessary
    echo "Invalid request method.";
}
?>
