<?php
session_start();
include ("connection.php");

// Ensure the user is logged in and an orderID is present
if (!isset($_SESSION['user_id']) || !isset($_GET['orderID'])) {
    header("Location: login.php");
    exit;
}
$orderID = $_GET['orderID'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Payment | Dazzling Donut</title>
</head>

<body>
    <!-- Payment Section Starts Here -->
    <div class="payment-container">
        <h1>Payment Details</h1>
        <form method="post" action="saveCustomerPayment.php">
            <input type="hidden" name="orderID" value="<?php echo htmlspecialchars($orderID); ?>">

            <!-- Choose Payment Method -->
            <div class="form-group">
                <label for="paymentMethod">Payment Method:</label>
                <select name="paymentMethod" id="paymentMethod" required>
                    <option value="">Select a Payment Method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Touch and Go">Touch N Go</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <!-- Card Details -->
            <div id="creditCardDetails">
                <div class="form-group">
                    <label for="cardName">Name on Card:</label>
                    <!-- Only allows alphabetic characters and spaces; input is required -->
                    <input type="text" id="cardName" name="cardName" required pattern="[A-Za-z\s]+"
                        title="Name must contain only letters and spaces.">
                </div>
                <div class="form-group">
                    <label for="cardNumber">Card Number:</label>
                    <!-- Only allows integers; input is required -->
                    <input type="text" id="cardNumber" name="cardNumber" required pattern="\d*"
                        title="Card number must contain only digits.">
                </div>
                <div class="form-group">
                    <label for="cvc">CVC:</label>
                    <!-- Only allows 3 digits; input is required -->
                    <input type="text" id="cvc" name="cvc" required pattern="\d{3}"
                        title="CVC must be a 3-digit number.">
                </div>
                <div class="form-group">
                    <label for="expiryDate">Expiry Date:</label>
                    <!-- Ensures format is MM/YY; input is required -->
                    <input type="text" id="expiryDate" name="expiryDate" required pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                        placeholder="MM/YY" title="Expiry date must be in MM/YY format.">
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="Submit Payment">
            </div>
        </form>
    </div>
    <!-- Payment Section Ends Here -->

</body>

</html>

<style>
    body {
        margin: 0;
        padding: 0;
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

    .payment-container {
        width: 100%;
        max-width: 400px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: rgba(255, 255, 255, 0.9);
        /* transparent */
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input[type="text"],
    .form-group input[type="submit"],
    .form-group select {
        width: 100%;
        padding: 8px;
        margin: 5px 0 20px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-group input[type="submit"] {
        background-color: #aaace6;
        color: white;
        cursor: pointer;
    }

    .form-group input[type="submit"]:hover {
        background-color: #9192b2;
    }

    #creditCardDetails {
        display: none;
    }
</style>

<script>
    //Function to handle changes payment method dropdown
    window.onload = function () {
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const creditCardDetails = document.getElementById('creditCardDetails');

        paymentMethodSelect.onchange = function () {
            creditCardDetails.style.display = this.value === 'Credit Card' ? 'block' : 'none';
        }
    }
</script>