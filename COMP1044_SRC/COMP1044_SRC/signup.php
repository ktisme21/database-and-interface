<?php
//Start session
session_start();

include ("connection.php");
include ("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!empty($name) && !empty($password) && !is_numeric($name) && !empty($email)) {
        // Check if the email already exists
        $stmt = $con->prepare("SELECT * FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, redirect to login
            header("Location: login.php");
            die("User with this email already exists. Redirecting to login page...");
        } else {
            // New user, proceed to insert
            $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $stmt = $con->prepare("INSERT INTO Users (Name, Email, Password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password_hash);
            if ($stmt->execute()) {
                // Redirect to login on successful signup
                header("Location: login.php");
                die;
            } else {
                echo "Error during registration: " . $stmt->error;
            }
        }
    } else {
        echo "Please enter some valid information!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up | Dazzling Donut</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!-- Sign up form Starts-->
    <div class="login-container">
        <h2>Sign Up</h2>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <input type="submit" value="Sign Up" class="btn btn-primary">
        </form>
        <div class="text-center signup-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
    <!-- Sign up form Ends-->
</body>
</html>