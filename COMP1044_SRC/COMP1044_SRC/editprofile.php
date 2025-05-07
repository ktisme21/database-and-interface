<?php
session_start(); //Start Session
include ("connection.php");
include ("functions.php");

$user_id = $_SESSION['user_id']; // Assuming you store user ID in session upon login
if (!$user_id) {
    // Redirect to login if not logged in
    header("Location: login.php");
    die;
}

// Fetch user data from database
$stmt = $con->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

$message = ''; // Message to display to the user

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Verify old password
    if (password_verify($old_password, $user_data['Password'])) {
        // Old password is correct, proceed with updating user info

        // Use password_hash for new password if changed
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $con->prepare("UPDATE Users SET Name = ?, Email = ?, Password = ? WHERE UserID = ?");
        $update_stmt->bind_param("sssi", $name, $email, $password_hash, $user_id);
        if ($update_stmt->execute()) {
            // Successfully updated, redirect to login page
            header("Location: login.php");
            die;
        } else {
            $message = "Error updating profile.";
        }
    } else {
        // Old password does not match
        $message = "Old password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Dazzling Donut</title>

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <!-- Profile Section Starts Here -->
    <div class="login-container">
        <h2>Edit Profile</h2>
        <?php if (!empty($message)): ?>
            <p><?= $message ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= $user_data['Name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= $user_data['Email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="old_password">Old Password:</label>
                <input type="password" id="old_password" name="old_password" placeholder="Enter old password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password"
                    placeholder="Enter new password (leave blank to keep current)">
            </div>
            <input type="submit" value="Update Profile" class="btn btn-primary">
        </form>
         <!-- Back to Homepage button below the form -->
         <div class="homepagebtn"><a href="index.php">Back To Homepage </a></div>
    </div>
    <!-- Profile Section Ends Here -->

</body>
</html>