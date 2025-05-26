<?php
// Database connection
if (!file_exists('db.php')) {
    echo json_encode(['success' => false, 'message' => 'db.php file not found']);
    exit;
}
include 'db.php';
header('Content-Type: application/json');

// Debug the connection
error_log("Connection object: " . var_export($conn, true));

// Check if database connection is successful
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$token = $_POST['token'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// Validate inputs
if (empty($token) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'Token and new password are required']);
    exit;
}

// Validate token
$stmt = $conn->prepare("SELECT email FROM admin_password_resets WHERE token = ? AND expires > NOW()");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
    exit;
}

$row = $result->fetch_assoc();
$email = $row['email'];

// Hash new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admins table
$updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$updateStmt->bind_param("ss", $hashed_password, $email);

if ($updateStmt->execute()) {
    // Delete used token
    $deleteStmt = $conn->prepare("DELETE FROM admin_password_resets WHERE email = ?");
    if (!$deleteStmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Password has been reset successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
}

$stmt->close();
$updateStmt->close();
$deleteStmt->close();
$conn->close();
?>