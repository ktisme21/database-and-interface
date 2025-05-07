<?php
// Start session
session_start();

//Check if user is logged in and unset the user_id session variable
if(isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

header("Location: index.php"); // Redirect to the home page after logout
die;//Stop
?>