<?php
//Session
session_start();

include("connection.php");

//Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//Redirect to order summary page
if (!isset($_GET['orderID'])) {
    header("Location: orderSummary.php");
    exit;
}

//Extract user ID and order ID
$userID = $_SESSION['user_id'];
$orderID = $_GET['orderID'];

try {
    // Start a transaction
    $con->begin_transaction();

    // Delete OrderDetails entries for the given order ID
    $deleteOrderDetails = "DELETE FROM OrderDetails WHERE OrderID = ? AND EXISTS (SELECT 1 FROM Orders WHERE OrderID = OrderDetails.OrderID AND UserID = ?)";
    $stmtDetails = $con->prepare($deleteOrderDetails);
    $stmtDetails->bind_param("ii", $orderID, $userID);
    $stmtDetails->execute();

    // Commit the transaction
    $con->commit();

    // Redirect to the homepage
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    // Roll back if there was an error
    $con->rollback();
    echo "An error occurred: " . $e->getMessage();
    // Optionally redirect to an error page or log the error
}
?>
