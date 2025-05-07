<?php
// Start session
session_start();
include ("connection.php");

// Ensure the user is logged in and an orderID is present
if (!isset($_SESSION['user_id']) || !isset($_GET['orderID'])) {
    header("Location: login.php");
    exit;
}

//Retrieve
$orderID = $_GET['orderID'];

//Check if order is checked out
if (isset($_GET['action']) && $_GET['action'] == 'completeCheckout') {
    $updateOrderStatus = "UPDATE Orders SET Status = 'Completed' WHERE OrderID = ?";
    $updateStmt = $con->prepare($updateOrderStatus);
    $updateStmt->bind_param("i", $orderID);
    $updateStmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Details | Dazzling Donut</title>
</head>

<body>

    <!-- Customer Details Section Starts Here -->
    <div class="contact-container">
        <div class="contact-info">
            <h1>Shipping Details</h1>
        </div>
        <div class="contact-form">
            <form method="post" action="saveCustomerDetails.php">
                <input type="hidden" name="orderID" value="<?php echo $orderID; ?>">
                <div class="form-group">
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>

                <div class="form-group">
                    <label for="phoneNumber">Contact Number:</label><br>
                    <input type="text" id="phoneNumber" name="phoneNumber" pattern="\d*"
                        title="Please enter numbers only." required><br>
                </div>

                <div class="form-group">
                    <label for="street">Address Line 1:</label><br>
                    <input type="text" id="street" name="street" pattern=".*\d+.*"
                        title="Please include both letters and numbers." required><br>
                </div>

                <div class="form-group">
                    <label for="street2">Address Line 2:</label><br>
                    <input type="text" id="street2" name="street2" pattern=".*"
                        title="Please include letters."><br>
                </div>

                <div class="form-group">
                    <label for="city">City:</label><br>
                    <input type="text" id="city" name="city" required><br>
                </div>

                <div class="form-group">
                    <label for="state">State:</label><br>
                    <input type="text" id="state" name="state" required><br>
                </div>

                <div class="form-group">
                    <label for="zipCode">Postal Code:</label><br>
                    <input type="text" id="zipCode" name="zipCode" pattern="\d*" title="Please enter numbers only."
                        required><br>
                </div>

                <div class="form-group">
                    <label for="country">Country:</label><br>
                    <input type="text" id="country" name="country" required><br>
                </div>

                <div class="form-group">
                    <input type="submit" value="Save Details" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
    <!-- Customer Details Section Ends Here -->

</body>

</html>

<style>
    body {
        padding: 100px;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-image: url('images/fulldonut5.webp');
        background-size: cover;
        /* Cover the entire page */
        background-position: center;
        /* Center the background image */
        background-repeat: no-repeat;
        /* Do not repeat the image */
    }

    .contact-container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 80%;
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input[type="text"] {
        width: 96%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    /* Submit button */
    .form-group input[type="submit"] {
        width: 99%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #aaace6;
        color: white;
        cursor: pointer;
    }

    .form-group input[type="submit"]:hover {
        background-color: #9192b2;
    }

    .contact-info h1 {
        text-align: center;
        margin-bottom: 20px;
    }
</style>