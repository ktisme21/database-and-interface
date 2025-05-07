<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['user_id'];

// Fetch orders, their details, and shipping details for the logged-in user
$query = "SELECT o.OrderID, o.OrderDate, o.TotalPrice, o.Status,
                 d.MenuItemID, d.Quantity, d.ItemPrice, m.Name,
                 s.RecipientName, s.AddressLine1, s.AddressLine2, s.City, s.State, s.PostalCode, s.Country, s.ContactNumber
          FROM Orders o
          JOIN OrderDetails d ON o.OrderID = d.OrderID
          JOIN MenuItems m ON d.MenuItemID = m.MenuItemID
          LEFT JOIN ShippingDetails s ON o.OrderID = s.OrderID
          WHERE o.UserID = ?
          ORDER BY o.OrderDate DESC";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$orders = []; // Array to store all orders
$currentOrderID = 0;
$currentOrder = null;

// Organize order details into orders
while ($row = $result->fetch_assoc()) {
    if ($currentOrderID != $row['OrderID']) {
        if ($currentOrder !== null) {
            $orders[] = $currentOrder; // Save the previous order
        }
        $currentOrderID = $row['OrderID'];
        $currentOrder = [
            'OrderID' => $row['OrderID'],
            'OrderDate' => $row['OrderDate'],
            'TotalPrice' => $row['TotalPrice'],
            'Status' => $row['Status'],
            'ShippingDetails' => [
                'RecipientName' => $row['RecipientName'],
                'AddressLine1' => $row['AddressLine1'],
                'AddressLine2' => $row['AddressLine2'],
                'City' => $row['City'],
                'State' => $row['State'],
                'PostalCode' => $row['PostalCode'],
                'Country' => $row['Country'],
                'ContactNumber' => $row['ContactNumber']
            ],
            'Items' => []
        ];
    }
    // Check if item already exists in current order
    $itemIndex = array_search($row['MenuItemID'], array_column($currentOrder['Items'], 'MenuItemID'));
    if ($itemIndex !== false) {
        // Update quantity and total item price
        $currentOrder['Items'][$itemIndex]['Quantity'] += $row['Quantity'];
        $currentOrder['Items'][$itemIndex]['TotalItemPrice'] += $row['Quantity'] * $row['ItemPrice'];
    } else {
        // Add new item
        $currentOrder['Items'][] = [
            'MenuItemID' => $row['MenuItemID'],
            'Name' => $row['Name'],
            'Quantity' => $row['Quantity'],
            'ItemPrice' => $row['ItemPrice'],
            'TotalItemPrice' => $row['Quantity'] * $row['ItemPrice']
        ];
    }
}
if ($currentOrder !== null) {
    $orders[] = $currentOrder; // Save the last order
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container">
    <h1>Your Order History</h1>
    <div class="homepagebtn"><a href="index.php">Back To Homepage </a></div>
    <?php if (empty($orders)): ?>
        <p>No order history found.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-container">
                <h2>Order #<?= htmlspecialchars($order['OrderID']) ?> - <?= htmlspecialchars($order['Status']) ?></h2>
                <p>Ordered on: <?= htmlspecialchars($order['OrderDate']) ?></p>
                <h3>Shipping Information:</h3>
                <ul>
                    <li>Name: <?= htmlspecialchars($order['ShippingDetails']['RecipientName']) ?></li>
                    <li>Address: <?= htmlspecialchars($order['ShippingDetails']['AddressLine1']) ?>
                        <?= !empty($order['ShippingDetails']['AddressLine2']) ? ', ' . htmlspecialchars($order['ShippingDetails']['AddressLine2']) : '' ?></li>
                    <li>City: <?= htmlspecialchars($order['ShippingDetails']['City']) ?></li>
                    <li>State: <?= htmlspecialchars($order['ShippingDetails']['State']) ?></li>
                    <li>Postal Code: <?= htmlspecialchars($order['ShippingDetails']['PostalCode']) ?></li>
                    <li>Country: <?= htmlspecialchars($order['ShippingDetails']['Country']) ?></li>
                    <li>Contact Number: <?= htmlspecialchars($order['ShippingDetails']['ContactNumber']) ?></li>
                </ul>
                <h3>Order Items:</h3>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price per Item</th>
                        <th>Total Price</th>
                    </tr>
                    <?php foreach ($order['Items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['Name']) ?></td>
                            <td><?= htmlspecialchars($item['Quantity']) ?></td>
                            <td>$<?= number_format($item['ItemPrice'], 2) ?></td>
                            <td>$<?= number_format($item['TotalItemPrice'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th colspan="3">Order Total</th>
                        <th>$<?= number_format($order['TotalPrice'], 2) ?></th>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</body>
</html>

<style>
html, body {
    /* height: 180%;  */
    margin: 0; /* Removes default margin */
    display: flex;
    justify-content: center; /* Centers content horizontally */
    align-items: center; /* Centers content vertically */
    background-color: #a8a3d1; /* Sets a light purple background color */
    font-family: Arial, sans-serif; /* Sets a clean, modern font */
}

.container {
    width: 80%; /* Responsive width, adjust as needed */
    max-width: 600px; /* Maximum width to maintain readability */
    padding: 120px;
    background: white; /* Background color of the content area */
    box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Subtle shadow for depth */
    border-radius: 10px; /* Rounded corners for a modern look */
    display: flex; /* Enables flexbox for this container */
    flex-direction: column; /* Stacks child elements vertically */
    align-items: center; /* Centers child elements horizontally */
}

.order-container {
    width: 100%;
    background: #f8f8f8;
    padding: 50px;
    margin-top: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease; /*Smooth transition for background color*/
}

.order-container:hover {
    background-color: #e0e0e0; /* Slightly darker on hover for better interaction feedback */
}

table {
    width: 100%; /* Full width of its container */
    border-collapse: collapse; /* Ensures borders between cells are merged */
    margin-top: 20px; /* Adds space above the table */
}

th, td {
    padding: 8px; /* Spacing inside cells */
    text-align: left; /* Aligns text to the left */
    border-bottom: 1px solid #ddd; /* Light grey line below each row */
}

th {
    background-color: #a8a3d1; /* Light grey background for headers */
}
</style>

