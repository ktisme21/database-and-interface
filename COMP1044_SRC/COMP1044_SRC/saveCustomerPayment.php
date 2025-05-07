<?php
//Start session
session_start();
include("connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check for POST data
if (isset($_POST['orderID'], $_POST['paymentMethod'])) {
    $orderID = $_POST['orderID'];
    $paymentMethod = $_POST['paymentMethod'];
    $paymentStatus = 'Completed'; // Assuming payment is completed successfully

    // Start transaction
    $con->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    try {
        // Fetch CartID for the OrderID
        $cartIdStmt = $con->prepare("SELECT CartID FROM Orders WHERE OrderID = ?");
        $cartIdStmt->bind_param("i", $orderID);
        $cartIdStmt->execute();
        $cartResult = $cartIdStmt->get_result();
        if ($cartRow = $cartResult->fetch_assoc()) {
            $cartID = $cartRow['CartID'];

            // Record payment with CartID
            $paymentStmt = $con->prepare("INSERT INTO Payments (OrderID, CartID, PaymentMethod, PaymentStatus) VALUES (?, ?, ?, ?)");
            $paymentStmt->bind_param("iiss", $orderID, $cartID, $paymentMethod, $paymentStatus);
            $paymentStmt->execute();

            // Handle inventory adjustment and cart item cleanup
            $cartItemsStmt = $con->prepare("SELECT MenuItemID, Quantity FROM CartItems WHERE CartID = ?");
            $cartItemsStmt->bind_param("i", $cartID);
            $cartItemsStmt->execute();
            $itemsResult = $cartItemsStmt->get_result();
            while ($item = $itemsResult->fetch_assoc()) {
                // Check stock availability
                $stockCheckStmt = $con->prepare("SELECT Quantity FROM MenuItems WHERE MenuItemID = ?");
                $stockCheckStmt->bind_param("i", $item['MenuItemID']);
                $stockCheckStmt->execute();
                $stockResult = $stockCheckStmt->get_result();
                if ($stockRow = $stockResult->fetch_assoc()) {
                    if ($stockRow['Quantity'] < $item['Quantity']) {
                        // Insufficient stock, rollback and error
                        throw new Exception("Insufficient stock for item ID: " . $item['MenuItemID']);
                    }

                    // Update inventory
                    $updateInventoryStmt = $con->prepare("UPDATE MenuItems SET Quantity = Quantity - ? WHERE MenuItemID = ?");
                    $updateInventoryStmt->bind_param("ii", $item['Quantity'], $item['MenuItemID']);
                    $updateInventoryStmt->execute();
                }
            }

            // Clear CartItems for this order
            $clearCartStmt = $con->prepare("DELETE FROM CartItems WHERE CartID = ?");
            $clearCartStmt->bind_param("i", $cartID);
            $clearCartStmt->execute();

             // Commit transaction
            $con->commit();
            // Display alert message and redirect using JavaScript
            echo "<script>alert('Payment Completed! Redirecting to home page...'); window.location.href = 'index.php';</script>";
            exit;
        } else {
            throw new Exception("Order not found.");
        }
    } catch (Exception $e) {
        $con->rollback();
        // Display error using JavaScript alert
        echo "<script>alert('Payment failed: " . addslashes($e->getMessage()) . "'); window.location.href = 'cart.php';</script>";
        exit;    
    }
} else {
    //Missing information error
    echo "<script>alert('Required information is missing.'); window.location.href = 'cart.php';</script>";
    exit;
}

?>
