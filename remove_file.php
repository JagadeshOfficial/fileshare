<?php
header('Content-Type: application/json'); // Set the response type to JSON

// Function to send a JSON response
function sendResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method. Use POST.');
}

// Get the raw POST data and decode it
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if the 'id' parameter is provided
if (!isset($data['id']) || empty($data['id'])) {
    sendResponse('error', 'File ID is required.');
}

$fileId = $data['id'];

// Example: Assuming you're deleting a file from the file system
// Replace this with your actual logic (e.g., database query or file system operation)
$filePath = "path/to/your/files/" . $fileId; // Adjust this path to where your files are stored

if (file_exists($filePath)) {
    if (unlink($filePath)) {
        // Optionally, delete the file record from a database (example with PDO)
        /*
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=your_db", "username", "password");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
            $stmt->execute([$fileId]);
        } catch (PDOException $e) {
            sendResponse('error', 'Database error: ' . $e->getMessage());
        }
        */
        sendResponse('success', 'File deleted successfully');
    } else {
        sendResponse('error', 'Failed to delete the file from the server.');
    }
} else {
    sendResponse('error', 'File not found.');
}
?>