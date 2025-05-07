<?php
session_start(); //Session Starts

include ("connection.php");
include ("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Read from database
        $stmt = $con->prepare("SELECT * FROM users WHERE Email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user_data['Password'])) {
                $_SESSION['user_id'] = $user_data['UserID'];

                // Cart logic starts here
                $userID = $_SESSION['user_id']; // Use the user ID from the session

                // Check if the user already has a cart
                $stmt = $con->prepare("SELECT CartID FROM Carts WHERE UserID = ?");
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    // User does not have a cart, create one
                    $insertCart = $con->prepare("INSERT INTO Carts (UserID) VALUES (?)");
                    $insertCart->bind_param("i", $userID);
                    $insertCart->execute();
                }
                // Cart logic ends here
                header("Location: index.php"); //Redirect to index.php
                die;

            } else {
                echo "Wrong email or password!";
            }
        } else {
            echo "Wrong email or password!";
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
    <title>Login | Dazzling Donut</title>

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!-- Login Section Starts -->
    <div class="login-container">
        <h2>Login to Your Account</h2>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <input type="submit" value="Login" class="btn btn-primary">
        </form>
        <div class="text-center signup-link">
            Don’t have an account? <a href="signup.php">Sign Up</a> <!-- Adjusted link based on folder structure -->
        </div>
    </div>
    <!-- Login Section Ends -->

</body>
</html>