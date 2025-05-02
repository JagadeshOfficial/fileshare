<?php
// Include your database connection file (adjust the path as needed)
include("db.php");

// Query to fetch all admins from the database
$query = "SELECT id, name, email, aadhaar FROM admins";
$result = $conn->query($query);

// Check if there are any results
if ($result->num_rows > 0) {
    // Output the data for each admin in the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['aadhaar'] . "</td>";
        echo "<td>";
        echo "<button onclick='openEditAdminForm(" . $row['id'] . ")' class='edit-btn'>Edit</button>";
        echo '<button onclick="deleteAdmin(' . $row['id'] . ')" class="delete-btn">Delete</button>';
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No admins found</td></tr>";
}

$conn->close();
?>
