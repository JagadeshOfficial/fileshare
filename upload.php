<?php
// Include your database connection file
include('db.php');  // Adjust the path as needed

// Check if the form was submitted and if a file is present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    // Check if the file is uploaded properly
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {

        // Specify the directory where files will be uploaded
        $uploadDir = 'uploads/';

        // Create the directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get file information
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileError = $_FILES['file']['error'];

        // Allowed file types (example: pdf, jpg, png)
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        // Get the file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check for upload errors
        if ($fileError === 0) {
            // Check if the file type is allowed
            if (!in_array($fileExt, $allowedTypes)) {
                echo json_encode(['status' => 'error', 'message' => 'File type not allowed.']);
                exit;
            }

            // Check if the file size is within limit (e.g., 5MB)
            if ($fileSize > 5242880) {
                echo json_encode(['status' => 'error', 'message' => 'File is too large.']);
                exit;
            }

            // Sanitize the file name to avoid conflicts or special characters
            $fileNameSanitized = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $fileName);
            $fileDestination = $uploadDir . $fileNameSanitized;

            // Move the uploaded file to the specified directory
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // Prepare and execute the database insertion with prepared statements to prevent SQL injection
                $fileNameSanitized = mysqli_real_escape_string($conn, $fileNameSanitized);
                $fileSize = mysqli_real_escape_string($conn, $fileSize);
                $fileExt = mysqli_real_escape_string($conn, $fileExt);

                // Insert the file details into the database
                $query = "INSERT INTO files (file_name, file_size, file_type) 
                          VALUES ('$fileNameSanitized', '$fileSize', '$fileExt')";

                if ($conn->query($query) === TRUE) {
                    echo json_encode(['status' => 'success', 'file' => $fileNameSanitized, 'fileSize' => $fileSize]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to insert file data into the database.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File upload error: ' . $fileError]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or invalid file upload.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
