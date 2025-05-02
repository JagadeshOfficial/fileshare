<?php
// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
include('db.php');

// Fetch the current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close DB connection
$stmt->close();
$conn->close();
?>

<!-- Edit Profile Section -->
<div id="EditProfile" class="section profile-section">
  <h2 class="section-title">Edit Profile</h2>
  <form action="update_profile.php" method="POST" enctype="multipart/form-data">
    <div class="profile-card">
      <div class="profile-left">
        <img src="<?php echo $user['profile_picture'] ?: 'https://via.placeholder.com/120'; ?>" alt="Profile Picture" class="profile-image" />
        <input type="file" name="profile_picture" />
      </div>
      <div class="profile-right">
        <div class="profile-details">
          <label for="full-name">Full Name:</label>
          <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required />
          
          <label for="email">Email:</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />
          
          <label for="mobile">Mobile:</label>
          <input type="text" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" />
          
          <label for="aadhaar">Aadhaar:</label>
          <input type="text" name="aadhaar" value="<?php echo htmlspecialchars($user['aadhaar']); ?>" />
        </div>
        <button type="submit" class="edit-profile-btn">Update Profile</button>
      </div>
    </div>
  </form>
</div>
