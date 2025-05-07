<?php
//Start session
session_start();

include("connection.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

//Check if user is logged in and required input parameters are provided
if (!isset($_SESSION['user_id'], $input['itemId'], $input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in, item ID, or quantity not provided.']);
    exit;
}

//Extract user id and input parameters
$userId = $_SESSION['user_id'];
$itemId = $input['itemId'];
$newQuantity = $input['quantity'];

// First, check the available stock for the item
$stockStmt = $con->prepare("SELECT Quantity FROM MenuItems WHERE MenuItemID = ?");
$stockStmt->bind_param("i", $itemId);
$stockStmt->execute();
$stockResult = $stockStmt->get_result();
if ($stockRow = $stockResult->fetch_assoc()) {
    $availableStock = $stockRow['Quantity'];
    if ($newQuantity > $availableStock) {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity in the cart: quantity exceeds available stock.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found.']);
    exit;
}

// If the stock is sufficient, proceed with updating the cart item quantity
$updateStmt = $con->prepare("UPDATE CartItems SET Quantity = ? WHERE MenuItemID = ? AND CartID = (SELECT CartID FROM Carts WHERE UserID = ?)");
$updateStmt->bind_param("iii", $newQuantity, $itemId, $userId);
$updateStmt->execute();

//Check if quantity update was successful
if ($updateStmt->affected_rows > 0) {
    // Calculate the new total price for the cart
    $priceStmt = $con->prepare("SELECT SUM(MenuItems.Price * CartItems.Quantity) AS TotalPrice FROM CartItems JOIN MenuItems ON CartItems.MenuItemID = MenuItems.MenuItemID WHERE CartItems.CartID = (SELECT CartID FROM Carts WHERE UserID = ?)");
    $priceStmt->bind_param("i", $userId);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    $total = $priceResult->fetch_assoc();
    $newTotalPrice = $total['TotalPrice'];

    echo json_encode(['success' => true, 'newTotalPrice' => number_format($newTotalPrice, 2, '.', ''), 'message' => 'Quantity updated.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity in the cart.']);
}
?>
