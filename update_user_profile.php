<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $aadhaar = mysqli_real_escape_string($conn, $_POST['aadhaar']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $uploadDir = 'profile_images/';
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $profileImage = null;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('Upload error.'); window.location.href = 'user_dashboard.php';</script>";
            exit();
        }

        if ($_FILES['profile_image']['size'] > $maxFileSize) {
            echo "<script>alert('File too large.'); window.location.href = 'user_dashboard.php';</script>";
            exit();
        }

        $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Invalid file type.'); window.location.href = 'user_dashboard.php';</script>";
            exit();
        }

        $uniqueName = uniqid('profile_', true) . '.' . $fileExtension;
        $uploadFile = $uploadDir . $uniqueName;

        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            echo "<script>alert('Image upload failed.'); window.location.href = 'user_dashboard.php';</script>";
            exit();
        }

        $profileImage = $uniqueName;
    }

    if ($profileImage) {
        $stmt = $conn->prepare("UPDATE users SET name=?, aadhaar=?, email=?, mobile=?, address=?, profile_image=? WHERE id=?");
        $stmt->bind_param("ssssssi", $name, $aadhaar, $email, $mobile, $address, $profileImage, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, aadhaar=?, email=?, mobile=?, address=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $aadhaar, $email, $mobile, $address, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'user_dashboard.php';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.location.href = 'user_dashboard.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'user_dashboard.php';</script>";
}
?>
