<?php
session_start();
include("connection.php");

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//Begin database transaction
$userID = $_SESSION['user_id'];
$con->begin_transaction();

try {
    //Check if the user has a cart
    $stmt = $con->prepare("SELECT CartID FROM Carts WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    //If no cart found, throw an exception
    if ($result->num_rows === 0) {
        throw new Exception("No cart found for the user.");
    }

    //Fetch cartID
    $cart = $result->fetch_assoc();
    $cartID = $cart['CartID'];

    $itemsQuery = "SELECT MenuItems.MenuItemID, MenuItems.Price, CartItems.Quantity FROM CartItems JOIN MenuItems ON CartItems.MenuItemID = MenuItems.MenuItemID WHERE CartItems.CartID = ?";
    $itemsStmt = $con->prepare($itemsQuery);
    $itemsStmt->bind_param("i", $cartID);
    $itemsStmt->execute();
    $itemsResult = $itemsStmt->get_result();

    //Calculate total price
    $totalPrice = 0;
    while ($row = $itemsResult->fetch_assoc()) {
        $totalPrice += $row['Price'] * $row['Quantity'];
    }

    //Check if there's an active order for the user
    $orderCheck = "SELECT OrderID FROM Orders WHERE UserID = ? AND Status != 'Completed' ORDER BY OrderDate DESC LIMIT 1";
    $orderCheckStmt = $con->prepare($orderCheck);
    $orderCheckStmt->bind_param("i", $userID);
    $orderCheckStmt->execute();
    $orderCheckResult = $orderCheckStmt->get_result();

    if ($orderCheckRow = $orderCheckResult->fetch_assoc()) {
        $orderID = $orderCheckRow['OrderID'];
        $updateOrder = "UPDATE Orders SET TotalPrice = ?, Status = 'Active' WHERE OrderID = ?";
        $updateStmt = $con->prepare($updateOrder);
        $updateStmt->bind_param("di", $totalPrice, $orderID);
        $updateStmt->execute();
    } else {
        $orderInsert = "INSERT INTO Orders (UserID, CartID, OrderDate, TotalPrice, Status) VALUES (?, ?, NOW(), ?, 'Active')";
        $orderStmt = $con->prepare($orderInsert);
        $orderStmt->bind_param("iid", $userID, $cartID, $totalPrice);
        $orderStmt->execute();
        $orderID = $orderStmt->insert_id;
    }

    //Insert or update order details
    foreach ($itemsResult as $item) {
        $detailsInsertOrUpdate = "REPLACE INTO OrderDetails (OrderID, CartID, MenuItemID, Quantity, ItemPrice) VALUES (?, ?, ?, ?, ?)";
        $detailsStmt = $con->prepare($detailsInsertOrUpdate);
        $detailsStmt->bind_param("iiiid", $orderID, $cartID, $item['MenuItemID'], $item['Quantity'], $item['Price']);
        $detailsStmt->execute();
    }

    //Commit transaction and redirect
    $con->commit();
    header("Location: orderSummary.php?orderID=" . $orderID);
    exit;

} catch (Exception $e) {
    //Roll back transaction in case of error
    $con->rollback();
    echo "Error: " . $e->getMessage();
}
?>
