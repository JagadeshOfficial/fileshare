<?php
// Include your database connection file
include('db.php');  // Adjust the path as needed

// Get the file ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$fileId = $data['id'];

if ($fileId) {
    // Fetch the file details from the database to get the file name
    $query = "SELECT file_name FROM files WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $fileId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $file = $result->fetch_assoc();
        $fileName = $file['file_name'];

        // Delete the file from the server
        $filePath = 'uploads/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);  // Delete the file from the server
        }

        // Delete the file record from the database
        $deleteQuery = "DELETE FROM files WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $fileId);

        if ($deleteStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'File deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete file from the database.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file ID provided.']);
}
?>
