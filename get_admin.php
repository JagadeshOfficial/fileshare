<?php
session_start();
include('db.php'); // Include your database connection

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if the id parameter is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid admin ID']);
    exit;
}

$adminId = (int)$_GET['id'];

// Fetch admin details
$stmt = $conn->prepare("SELECT id, name, email, mobile, aadhaar, profile_picture FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo json_encode($admin);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
}

$stmt->close();
$conn->close();
?>