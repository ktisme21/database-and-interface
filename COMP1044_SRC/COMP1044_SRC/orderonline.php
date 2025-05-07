<?php
//Session Starts
session_start();

// Include your database connection and functions here
include("connection.php");
include("functions.php");

// Check if the user is already logged in
if(isset($_SESSION['user_id'])) {
    // If logged in, redirect to menu page
    header("Location: menu.php");
    exit();
} else {
    // If not logged in, redirect to login page
    // Consider passing a 'redirect' parameter to tell the login page where to go after a successful login
    header("Location: login.php?redirect=menu.php");
    exit();
}
?>
