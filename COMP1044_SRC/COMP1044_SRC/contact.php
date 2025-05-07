<?php
//Session Start used to begin a session
session_start();

// show a warning when we use include, fatal error will come out when we use require, the script will stop
include ("connection.php");
include ("functions.php");


// Fetch cart items for the logged-in user by joining the Carts and CartItems tables
$cartItemsQuery = "SELECT MenuItems.Name, MenuItems.Price, SUM(CartItems.Quantity) AS Quantity, MenuItems.MenuItemID
                   FROM CartItems
                   JOIN MenuItems ON CartItems.MenuItemID = MenuItems.MenuItemID
                   JOIN Carts ON CartItems.CartID = Carts.CartID
                   WHERE Carts.UserID = ?
                   GROUP BY MenuItems.MenuItemID"; //Query to fetch cart item
$stmt = $con->prepare($cartItemsQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC); //Fetch

// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['Price'] * $item['Quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Dazzling Donut</title>

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!-- Navbar Section Starts Here -->
    <section class="navbar">
        <div class="main-list">
            <ul class="list">
                <li><a href="index.php">Home</a></li>
                <!-- <li><a href="menu.php">Menu</a></li> -->
                <li class="dropdown">
                        <a href="javascript:void(0)" class="dropbtn">Menu</a>
                        <div class="dropdown-content">
                            <a href="menu.php">All</a>
                            <a href="category.php?category=Donuts">Donut</a>
                            <a href="category.php?category=Ice%20Cream">Ice Cream</a>
                            <a href="category.php?category=Drinks">Drinks</a>
                            <a href="category.php?category=Merchandise">Merchandise</a>
                        </div>
                    </li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>

        <div class="logo">
            <a href="index.php" title="Logo">
                <img src="images/logo.png" alt="Restaurant Logo" class="img-responsive">
            </a>
        </div>

        <div class="user-list">
            <ul class="list">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Log Out</a></li>
                    <!-- <li><a href="editprofile.php">Edit Profile</a><li> -->
                    <li class="dropdown">
                    <a href="javascript:void(0)" class="dropbtn"><i class="fa fa-user-secret"></i></a>
                        <div class="dropdown-content">
                            <a href="editprofile.php">Edit Profile</a>
                            <a href="orderHistory.php">Order History</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="login.php">Log In</a></li>
                <?php endif; ?>
                <li><a href="menu.php" class="order-online">Order Online</a></li>
                </div>
            </ul>
        </div>

        <div class="clearfix"></div>

    </section>
    <!-- Navbar Section Ends Here -->

    <!-- Contact Section Start-->
    <div class="contact-container">
        <div class="contact-info">
            <h1>Contact us</h1>
            <p>Need to get in touch with us? Either fill out the form with your inquiry or find the department email
                you'd like to contact below.</p>
        </div>
        <div class="contact-form">
            <form>
                <div class="form-group">
                    <label for="name">Name*</label>
                    <input type="text" id="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" id="email" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label for="inquiry">What can we help you with?</label>
                    <textarea id="inquiry"></textarea>
                </div>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <!-- Contact Section Ends-->

    <!-- Footer Starts-->
    <footer class="footer">
        <div class="footer-content">
            <!-- About -->
            <div class="footer-section about">
                <h3>Visit Our Store</h3>
                <p><span class="underline">Address</span></p>
                <p>No. 9, Jalan Gemilang Bakri 1, <br>Pusat Komersial Gemilang Bakri, <br>Bakri, Muar.</p>
                <p><span class="underline">Contact</span></p>
                <p>info@dazzlingdonutshop.com</p>
                <p>012-276 1384</p>
            </div>

            <!-- Opening Hours -->
            <div class="footer-section hours">
                <h3>Opening Hours</h3>
                <p>Sun - Thu: 10am - 10pm</p>
                <p>Fri - Sat: 10am - 11pm</p>
                <br><br>
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="https://www.facebook.com/UoNMalaysia" target="_blank" class="fa fa-facebook"
                        aria-label="Facebook"></a>
                    <a href="https://www.instagram.com/uonmalaysia/" target="_blank" class="fa fa-instagram"
                        aria-label="Instagram"></a>
                    <a href="https://twitter.com/elonmusk" target="_blank" class="fa fa-twitter"
                        aria-label="Twitter"></a>
                </div>
            </div>
            <!-- Newsletter -->
            <div class="footer-section newsletter">
                <h3>Subscribe to our newsletter for some sweet stuff</h3>
                <form>
                    <input type="email" name="email" placeholder="Email">
                    <button type="submit">Subscribe</button>
                    <p>Thanks for submitting!</p>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2023 by Dazzling Donut. Powered and secured by
                <a href="https://en.wikipedia.org/wiki/J._Robert_Oppenheimer" target="_blank" class="creator">Janice</a> &
                <a href="https://en.wikipedia.org/wiki/Niels_Bohr" target="_blank" class="creator"> Kei</a>
            </p>
        </div>
    </footer>
    <!-- Footer Section Ends Here -->

    <script src="index.js"></script>

</body>
</html>