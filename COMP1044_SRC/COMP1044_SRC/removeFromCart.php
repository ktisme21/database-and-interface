<?php
//Start session
session_start();
include("connection.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

//Check if user is logged in and itemID is provided
if (!isset($_SESSION['user_id'], $input['itemId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in or item ID not provided.']);
    exit;
}

$userId = $_SESSION['user_id'];
$itemId = $input['itemId'];

// First, fetch the price for the item being removed to calculate the removed price
$stmt = $con->prepare("SELECT CartItems.Quantity, MenuItems.Price FROM CartItems INNER JOIN MenuItems ON CartItems.MenuItemID = MenuItems.MenuItemID WHERE CartItems.MenuItemID = ? AND CartItems.CartID = (SELECT CartID FROM Carts WHERE UserID = ?)");
$stmt->bind_param("ii", $itemId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $removedPrice = $row['Price'] * $row['Quantity'];
    
    // Now, remove the item from the cart
    $deleteStmt = $con->prepare("DELETE FROM CartItems WHERE MenuItemID = ? AND CartID = (SELECT CartID FROM Carts WHERE UserID = ?)");
    $deleteStmt->bind_param("ii", $itemId, $userId);
    $delete = $deleteStmt->execute();
    
    if ($delete) {
        // Recalculate the total price of the cart after the item is removed
        $totalStmt = $con->prepare("SELECT SUM(MenuItems.Price * CartItems.Quantity) AS TotalPrice FROM CartItems INNER JOIN MenuItems ON CartItems.MenuItemID = MenuItems.MenuItemID WHERE CartItems.CartID = (SELECT CartID FROM Carts WHERE UserID = ?)");
        $totalStmt->bind_param("i", $userId);
        $totalStmt->execute();
        $totalResult = $totalStmt->get_result();
        $totalRow = $totalResult->fetch_assoc();
        $newTotalPrice = $totalRow['TotalPrice'] ?: 0; // Use 0 if no items are left

        //Respond with JSON
        echo json_encode([
            'success' => true,
            'removedPrice' => $removedPrice,
            'newTotalPrice' => number_format($newTotalPrice, 2, '.', ''),
            'message' => 'Item removed successfully.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item from the cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found in cart.']);
}
?>
