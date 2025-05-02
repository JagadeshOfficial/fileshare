<?php
ob_start();
include('db.php');
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT id, name, email, mobile, aadhaar, profile_picture FROM admins WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    ob_end_clean();
    echo json_encode($admin ?: []);
} else {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'No admin ID provided']);
}

$stmt->close();
$conn->close();
?>