<?php
//Start Session
session_start();
include ("connection.php");

//Check if user is logged in and orderID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['orderID'])) {
    header("Location: login.php");
    exit;
}

$orderID = $_GET['orderID'];

// Updated SQL query to also select the OrderDate
$query = "
SELECT o.OrderDate, mi.Name AS MenuItemName, SUM(od.Quantity) AS Quantity, od.ItemPrice, SUM(od.Quantity * od.ItemPrice) AS TotalPrice, 
       sd.RecipientName, sd.AddressLine1, sd.AddressLine2, sd.City, sd.State, sd.PostalCode, sd.Country, sd.ContactNumber
FROM Orders o
INNER JOIN OrderDetails od ON o.OrderID = od.OrderID
INNER JOIN MenuItems mi ON od.MenuItemID = mi.MenuItemID
INNER JOIN ShippingDetails sd ON o.OrderID = sd.OrderID
WHERE o.OrderID = ?
GROUP BY o.OrderDate, mi.Name, od.ItemPrice, sd.RecipientName, sd.AddressLine1, sd.AddressLine2, sd.City, sd.State, sd.PostalCode, sd.Country, sd.ContactNumber";

$stmt = $con->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $con->error);
}

$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No details found for Order ID: " . $orderID);// Check for error in preparing statement
}

$items = []; // Initialize array to hold order items
$customerInfo = []; // Initialize array to hold customer information
$overallTotal = 0; // Initialize a variable to hold the overall total
$orderDate = null; // Initialize $orderDate as null

// Fetch the results
while ($row = $result->fetch_assoc()) {
    // Aggregating only needed once, so taking the first set for customer info
    if (empty($customerInfo)) {
        $customerInfo = [
            'RecipientName' => $row['RecipientName'],
            'AddressLine1' => $row['AddressLine1'],
            'AddressLine2' => $row['AddressLine2'],
            'City' => $row['City'],
            'State' => $row['State'],
            'PostalCode' => $row['PostalCode'],
            'Country' => $row['Country'],
            'ContactNumber' => $row['ContactNumber']
        ];
    }
    //Extracting order date
    if (!$orderDate) { // Only set the first time
        $orderDate = new DateTime($row['OrderDate']);
    }
    $items[] = $row;
    $overallTotal += $row['TotalPrice']; // Sum up the TotalPrice of each aggregated item
}

//Close statement and connection
$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details | Dazzling Donut</title>
</head>

<body>
    <!-- Order Receipt Section Starts Here -->
    <div class="container">
        <h2>Order Details for Order ID: <?php echo htmlspecialchars($orderID); ?></h2>
        <?php if ($orderDate): ?>
            <p>Order Date: <?php echo $orderDate->format('F j, Y, g:i a'); ?></p>
        <?php endif; ?>
        <h3>Customer Information:</h3>
        <ul>
            <li>Name: <?php echo htmlspecialchars($customerInfo['RecipientName']); ?></li>
            <li>Address:
                <?php echo htmlspecialchars($customerInfo['AddressLine1']) . (empty($customerInfo['AddressLine2']) ? '' : ", " . htmlspecialchars($customerInfo['AddressLine2'])); ?>
            </li>
            <li>City: <?php echo htmlspecialchars($customerInfo['City']); ?></li>
            <li>State: <?php echo htmlspecialchars($customerInfo['State']); ?></li>
            <li>Postal Code: <?php echo htmlspecialchars($customerInfo['PostalCode']); ?></li>
            <li>Country: <?php echo htmlspecialchars($customerInfo['Country']); ?></li>
            <li>Contact Number: <?php echo htmlspecialchars($customerInfo['ContactNumber']); ?></li>
        </ul>
        <h3>Order Items:</h3>
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
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['MenuItemName']); ?></td>
                        <td><?php echo $item['Quantity']; ?></td>
                        <td>$<?php echo number_format($item['ItemPrice'], 2); ?></td>
                        <td>$<?php echo number_format($item['TotalPrice'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align: right;">Overall Total:</th>
                    <th>$<?php echo number_format($overallTotal, 2); ?></th>
                </tr>
            </tfoot>
        </table>
        <button class="proceed-button"
            onclick="window.location.href='customerPayment.php?orderID=<?php echo $orderID; ?>'">Proceed to
            Payment</button>
            <button class="download-button" onclick="window.print();">Download as PDF</button>
    </div>

    
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
        min-height: 100vh;
        background-image: url('images/fulldonut5.webp');
        background-size: cover;/* Cover the entire page */
        background-position: center;/* Center the background image */
        background-repeat: no-repeat;/* Do not repeat the image */
    }

    .container {
        width: 80%;
        max-width: 800px;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    table {
        margin-left: auto;/* Center-align the table */
        margin-right: auto;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #aaace6;
    }

    .proceed-button {
        margin-top: 20px;
        padding: 10px 20px;
        font-size: 16px;
        background-color: #aaace6;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .proceed-button:hover {
        background-color: #9192b2;
    }
    
    .download-button {
        cursor: pointer;
        padding: 0px 5px;
    }

    h2 {
        text-align: center;
    }
</style>