<?php
// Include your database connection file
include('db.php');  // Adjust the path as needed

// Set the response type to JSON
header('Content-Type: application/json');

// Fetch the list of uploaded files from the database
$query = "SELECT * FROM files";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $fileList = [];
    while ($row = $result->fetch_assoc()) {
        $fileList[] = [
            'id' => $row['id'],
            'file_name' => $row['file_name'], // Changed from 'name' to 'file_name'
            'file_size' => number_format($row['file_size'] / 1024, 2) . ' KB', // Changed from 'size' to 'file_size'
            'file_type' => $row['file_type'], // Changed from 'type' to 'file_type'
            'upload_date' => $row['upload_date'],
        ];
    }
    echo json_encode(['status' => 'success', 'files' => $fileList]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No files found.']);
}
?>