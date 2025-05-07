<?php
//Start session
session_start();

include("connection.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if POST data is received
if (isset($_POST['orderID'], $_POST['fullName'], $_POST['phoneNumber'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zipCode'], $_POST['country'])) {
    $orderID = $_POST['orderID'];
    $fullName = $_POST['fullName'];
    $phoneNumber = $_POST['phoneNumber'];
    $addressLine1 = $_POST['street']; // Assuming 'street' is AddressLine1 based on your customerDetails.php
    $addressLine2 = $_POST['street2']; // Make sure you have a field named 'street2' for AddressLine2 in your form
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zipCode'];
    $country = $_POST['country'];

    // Prepare SQL query
    $query = "INSERT INTO ShippingDetails (OrderID, RecipientName, AddressLine1, AddressLine2, City, State, PostalCode, Country, ContactNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $con->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("issssssss", $orderID, $fullName, $addressLine1, $addressLine2, $city, $state, $zipCode, $country, $phoneNumber);
        
        // Execute statement
        if ($stmt->execute()) {
            // Redirect to a confirmation page or order summary page
            header("Location: overallSummary.php?orderID=" . $orderID);
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }
} else {
    echo "Required information is missing.";
}

//Close connection
$con->close();
?>
