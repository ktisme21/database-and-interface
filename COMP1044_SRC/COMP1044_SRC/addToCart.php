<?php
//Session Start
session_start();

// Include your database connection script
include("connection.php");

//If item ID and user ID are set
if (isset($_POST['itemId'], $_SESSION['user_id'])) {
    $itemId = $_POST['itemId'];
    $quantity = 1; // Example quantity
    
    // Fetch user's CartID
    $stmt = $con->prepare("SELECT CartID FROM Carts WHERE CartID = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();
    
    //If the user's CartID exists
    if ($cart) {
        $cartID = $cart['CartID'];
        
        // Insert item into CartItems
        $insertItem = $con->prepare("INSERT INTO CartItems (CartID, MenuItemID, Quantity) VALUES (?, ?, ?)");
        $insertItem->bind_param("iii", $cartID, $itemId, $quantity);
        $insertItem->execute();
        
        echo "Item added to cart successfully!";
    } else {
        //If the cart not exists
        echo "Cart not found.";
    }
} else {
    echo "Item ID not provided or user not logged in.";
}
?>