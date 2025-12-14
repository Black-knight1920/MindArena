<?php
session_start();
require_once __DIR__ . '/../../config/bootstrap.php';

// If user session is not set redirect to login
if (!isset($_SESSION["username"])) {
    header("Location: " . BASE_URL . "/index.php?action=login");
    exit();
}

// Get the user's current profile data from the session or database
$username = $_SESSION["username"];
$email    = $_SESSION["email"];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the input data
    $newUsername = htmlspecialchars($_POST['username']);
    $newEmail = htmlspecialchars($_POST['email']);

    try {
        $query = "UPDATE user SET name = :username, email = :email WHERE name = :old_username";
        $stmt = $conn->prepare($query);
        $stmt->execute(array(
            ':username' => $newUsername,
            ':email' => $newEmail,
            ':old_username' => $username
        ));
        
        // Update the session variables after successful update
        $_SESSION['username'] = $newUsername;
        $_SESSION['email'] = $newEmail;

        // Set success message
        $message = "Profile updated successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        // Handle database error
        $message = "Error updating profile. Please try again.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/css/user_home.css">
</head>

<body>

<div class="container">

    <!-- Header with username -->
    <header>
    <div class="user-info" onclick="openModal()">
        dY` <?php echo htmlspecialchars($username); ?>
    </div>

    <a href="<?= BASE_URL ?>/index.php?action=login" class="logout-btn">Logout</a>
</header>

    <!-- Main section -->
    <div class="content">
        <h1>COMING SOON</h1>
        <p>Your personalized Mind Arena dashboard is under construction.</p>
    </div>
</div>

<!-- PROFILE MODAL -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <h2>Your Profile</h2>

        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

        <!-- Edit Button -->
        <button class="edit-btn" onclick="openEditModal()">Edit</button>
        
        <p>dYZ- Full profile integration soon...</p>
    </div>
</div>

<!-- EDIT PROFILE MODAL -->
<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>

        <h2>Edit Your Profile</h2>

        <!-- Display success or error message -->
        <?php if (isset($message)): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Profile Edit Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="editUsername">Username:</label>
                <input type="text" id="editUsername" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById("profileModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("profileModal").style.display = "none";
}

function openEditModal() {
    document.getElementById("editProfileModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editProfileModal").style.display = "none";
}
</script>

</body>
</html>

