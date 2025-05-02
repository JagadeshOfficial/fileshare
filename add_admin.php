<?php
include('db.php');
header('Content-Type: application/json');

$fullName = $_POST['fullName'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$aadhar = $_POST['aadhar'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (name, email, mobile, aadhaar, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $fullName, $email, $mobile, $aadhar, $password);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Admin added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add admin']);
}

$stmt->close();
$conn->close();
?>