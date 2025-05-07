<?php
//Session Start
session_start();

include ("connection.php");
include ("functions.php");

// Fetch the category name from the URL, default to Donuts if not specified
$categoryName = isset($_GET['category']) ? $_GET['category'] : 'Donuts';

// Prepare and execute the query to get the CategoryID based on the name
$categoryQuery = $con->prepare("SELECT CategoryID FROM Categories WHERE Name = ?");
$categoryQuery->bind_param("s", $categoryName);
$categoryQuery->execute();
$result = $categoryQuery->get_result();
$category = $result->fetch_assoc();

//If category exists
$items = [];
if ($category) {
    $categoryId = $category['CategoryID'];

    // Fetch the MenuItems for this category
    $menuItemsQuery = $con->prepare("SELECT * FROM MenuItems WHERE CategoryID = ? ORDER BY Name");
    $menuItemsQuery->bind_param("i", $categoryId);
    $menuItemsQuery->execute();
    $menuItemsResult = $menuItemsQuery->get_result();
    if ($menuItemsResult->num_rows > 0) {
        $items = $menuItemsResult->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<p>No items found in '{$categoryName}' category.</p>";
    }
} else {
    echo "<p>Category not found.</p>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoryName); ?> | Dazzling Donut</title>

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!-- Navbar Section Starts Here -->
    <section class="navbar">
        <div class="main-list">
            <ul class="list">
                <li><a href="index.php">Home</a></li>
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
                <!-- <li><a href="menu.php">Menu</a></li> -->
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
                    <!-- <li><a href="editprofile.php">Edit Profile</a></li>This is the edit profile button/link -->
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
            
            </ul>
        </div>

        <div class="clearfix"></div>
    </section>
    <!-- Navbar Section Ends Here -->

    <!-- Menu Section Ends Here -->
    <section class="food-menu2">
        <h2 class="text-center"><?php echo htmlspecialchars($categoryName); ?> Menu</h2>
        <div class="container2">
            <?php foreach ($items as $item): ?>
                <div class="food-menu-box">
                    <div class="food-menu-img">
                        <img src="<?php echo htmlspecialchars($item['Image']); ?>"
                            alt="<?php echo htmlspecialchars($item['Name']); ?>" class="img-responsive img-curve">
                    </div>
                    <div class="food-menu-desc">
                        <h4><?php echo htmlspecialchars($item['Name']); ?></h4>
                        <p class="food-price">$<?php echo htmlspecialchars($item['Price']); ?></p>
                        <p class="food-detail"><?php echo htmlspecialchars($item['Description']); ?></p>
                        <?php if ($item['Quantity'] > 0): ?>
                            <!-- Display quantity if available -->
                            <p class="food-quantity">Quantity Available: <?php echo $item['Quantity']; ?></p>
                            <button onclick="addToCart(<?php echo $item['MenuItemID']; ?>)" class="btn btn-primary">Add to
                                Cart</button>
                        <?php else: ?>
                            <p class="food-quantity">Not Available</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- Menu Section Ends Here -->

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