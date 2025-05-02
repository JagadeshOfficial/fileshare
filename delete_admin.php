<?php
// Include your database connection file
include("db.php");

// Check if an ID is provided
if (isset($_POST['id'])) {
    $adminId = $_POST['id'];

    // Prepare the SQL query to delete the admin
    $query = "DELETE FROM admins WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $adminId);

    // Execute the query
    if ($stmt->execute()) {
        echo 'success'; // Return success message
    } else {
        echo 'error'; // Return error message
    }

    $stmt->close();
} else {
    echo 'No admin ID provided';
}

$conn->close();
?>
