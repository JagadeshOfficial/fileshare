<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user input
$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$aadhaar = $_POST['aadhaar'];

// Handle profile picture upload
$profile_picture = $_FILES['profile_picture'];
if ($profile_picture['error'] === 0) {
    $file_name = $profile_picture['name'];
    $file_tmp = $profile_picture['tmp_name'];
    $file_path = 'uploads/' . basename($file_name); // Path to save image

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($file_tmp, $file_path)) {
        $profile_picture = $file_path;
    } else {
        $profile_picture = null;
    }
} else {
    $profile_picture = null;
}

// Update user data in database
$sql = "UPDATE users SET full_name = ?, email = ?, mobile = ?, aadhaar = ?, profile_picture = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $full_name, $email, $mobile, $aadhaar, $profile_picture, $user_id);
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo "Profile updated successfully!";
} else {
    echo "No changes were made!";
}

// Close DB connection
$stmt->close();
$conn->close();
?>
