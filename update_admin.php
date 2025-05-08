<?php
ob_start();
include("db.php");
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Validate required POST fields
$required_fields = ['editAdminIndex', 'editAdminName', 'editAdminEmail', 'editAdminMobile', 'editAdminAadhar'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => "Missing or empty field: $field"]);
        exit;
    }
}

$adminId = intval($_POST['editAdminIndex']);
$adminName = trim($_POST['editAdminName']);
$adminEmail = trim($_POST['editAdminEmail']);
$adminMobile = trim($_POST['editAdminMobile']);
$adminAadhar = trim($_POST['editAdminAadhar']);

// Fetch existing profile picture
$stmt = $conn->prepare("SELECT profile_picture FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
    $stmt->close();
    $conn->close();
    exit;
}
$existing = $result->fetch_assoc();
$profilePicture = $existing['profile_picture'];
$stmt->close();

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'Uploads/';
    // Ensure upload directory exists and is writable
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
            exit;
        }
    }
    if (!is_writable($uploadDir)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Upload directory is not writable']);
        exit;
    }

    $fileName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $_FILES['profile_picture']['name']);
    $filePath = $uploadDir . $adminId . '_' . time() . '_' . $fileName; // Add timestamp to avoid conflicts
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Validate file type
    if (!in_array($fileExt, $allowedTypes)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Invalid image format. Allowed types: jpg, jpeg, png, gif']);
        exit;
    }

    // Validate file size (5MB limit)
    if ($_FILES['profile_picture']['size'] > 5242880) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Image size exceeds 5MB limit']);
        exit;
    }

    // Attempt to move the uploaded file
    if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
        exit;
    }

    // Delete old profile picture if it exists and is not the default
    if ($profilePicture && file_exists($profilePicture) && $profilePicture !== 'https://via.placeholder.com/120') {
        if (!unlink($profilePicture)) {
            error_log("Failed to delete old profile picture: $profilePicture");
        }
    }

    $profilePicture = $filePath;
}

// Update admin details
$stmt = $conn->prepare("UPDATE admins SET name = ?, email = ?, mobile = ?, aadhaar = ?, profile_picture = ? WHERE id = ?");
$stmt->bind_param("sssssi", $adminName, $adminEmail, $adminMobile, $adminAadhar, $profilePicture, $adminId);

if ($stmt->execute()) {
    ob_end_clean();
    echo json_encode(['status' => 'success', 'message' => 'Admin updated successfully']);
} else {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Failed to update admin: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>