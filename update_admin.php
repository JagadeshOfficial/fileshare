<?php
ob_start();
include("db.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminId = $_POST['editAdminIndex'];
    $adminName = $_POST['editAdminName'];
    $adminEmail = $_POST['editAdminEmail'];
    $adminMobile = $_POST['editAdminMobile'];
    $adminAadhar = $_POST['editAdminAadhar'];

    // Fetch existing profile picture
    $stmt = $conn->prepare("SELECT profile_picture FROM admins WHERE id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();
    $profilePicture = $existing['profile_picture'];
    $stmt->close();

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'Uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $_FILES['profile_picture']['name']);
        $filePath = $uploadDir . $adminId . '_' . $fileName; // Prefix with admin ID to avoid conflicts
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowedTypes)) {
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Invalid image format']);
            exit;
        }

        if ($_FILES['profile_picture']['size'] > 5242880) { // 5MB limit
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Image too large']);
            exit;
        }

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
            // Delete old profile picture if it exists
            if ($profilePicture && file_exists($profilePicture)) {
                unlink($profilePicture);
            }
            $profilePicture = $filePath;
        } else {
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
            exit;
        }
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
} else {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>