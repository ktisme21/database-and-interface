<?php
session_start();
include ("connection.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['orderID'])) {
    header("Location: login.php");
    exit;
}

$orderID = $_GET['orderID'];

// Modified query to fetch CartID, Order Details ... 
$query = "SELECT SUM(OrderDetails.Quantity) AS TotalQuantity, OrderDetails.ItemPrice, MenuItems.Name, Orders.OrderDate, Orders.CartID
          FROM OrderDetails
          JOIN MenuItems ON OrderDetails.MenuItemID = MenuItems.MenuItemID
          JOIN Orders ON OrderDetails.OrderID = Orders.OrderID
          WHERE OrderDetails.OrderID = ?
          GROUP BY OrderDetails.ItemPrice, MenuItems.Name, Orders.OrderDate, Orders.CartID";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch order date from the first row since all rows will have the same order date for a given order ID
$orderDateRow = $result->fetch_assoc();
$orderDate = isset($orderDateRow['OrderDate']) ? date("F j, Y, g:i a", strtotime($orderDateRow['OrderDate'])) : "Date not available";
$cartID = isset($orderDateRow['CartID']) ? $orderDateRow['CartID'] : null; // Define $cartID here
$result->data_seek(0); // Reset result pointer to fetch items

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Order Summary | Dazzling Donut</title>
</head>

<body>
    <!-- Order Summary Starts Here -->
    <div class="order-summary">
        <h2>Order Summary</h2>
        <p class="order-id">Order ID: <?php echo htmlspecialchars($orderID); ?></p>
        <p class="order-date">Order Date: <?php echo $orderDate; ?></p>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price per Item</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPrice = 0;
                while ($item = $result->fetch_assoc()) {
                    $itemTotal = $item['ItemPrice'] * $item['TotalQuantity'];
                    $totalPrice += $itemTotal;

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['Name']) . "</td>";
                    echo "<td>" . $item['TotalQuantity'] . "</td>";
                    echo "<td>$" . number_format($item['ItemPrice'], 2) . "</td>";
                    echo "<td>$" . number_format($itemTotal, 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Order Total:</th>
                    <th>$<?php echo number_format($totalPrice, 2); ?></th>
                </tr>
            </tfoot>
        </table>
        <a href="backToHomepage.php?orderID=<?php echo htmlspecialchars($orderID); ?>"
            onclick="return confirm('Are you sure you want to cancel this order and return to the homepage?');">Back to
            Homepage</a>
        <a href="customerDetails.php?orderID=<?php echo htmlspecialchars($orderID); ?>&action=completeCheckout"
            onclick="return confirm('Once you confirm checkout, you will not be able to go back to the homepage. Continue?');">Confirm
            Checkout</a>
    </div>
    <!-- Order Summary Ends Here -->
</body>

</html>

<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;/* Adding a background image */
        background-image: url('images/fulldonut5.webp');
        background-size: cover;/* Cover the entire page */
        background-position: center;
        /* Center the background image */
        background-repeat: no-repeat;
        /* Do not repeat the image */
    }

    .order-summary {
        text-align: center;
        border: 2px solid #ddd;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        /* transparent */
        background-color: rgba(255, 255, 255, 0.9);

    }

    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ddd;
        text-align: left;
        padding: 8px;
    }

    th {
        background-color: #aaace6;
        color: black;
    }

    a {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        background-color: #aaace6;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
    }

    a:hover {
        background-color: #9192b2;
    }
</style>