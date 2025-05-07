<?php
session_start(); //Session Start

include ("connection.php");
include ("functions.php");

// Check if user is logged in 
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

//Fetch user ID
$userID = $_SESSION['user_id'];

$limitQuery = "SELECT * FROM MenuItems ORDER BY RAND() LIMIT 4"; // Adjust the limit as per your requirement
$limitResult = mysqli_query($con, $limitQuery);
$limitedMenuItems = mysqli_fetch_all($limitResult, MYSQLI_ASSOC);

// Fetch items from specific categories
$categories = ['Donuts', 'Ice Cream', 'Drinks'];
$topFlavors = [];

foreach ($categories as $category) {
    $query = "SELECT MenuItems.Name, MenuItems.Image FROM MenuItems
            JOIN Categories ON MenuItems.CategoryID = Categories.CategoryID
            WHERE Categories.Name = ? LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $topFlavors[] = $result->fetch_assoc(); //Fetch top flavor and add to array
    }
}

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
    <!-- Important to make website responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dazzling Donut</title>

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
                <!-- User-specific links -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Log Out</a></li>
                    <!-- <li><a href="editprofile.php">Edit Profile</a></li> -->
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

                <!-- Cart Icon Button -->
                <li><button id="cart-btn" onclick="toggleCart()" class="cart-icon-button">
                        <i class="fa fa-shopping-cart"></i>
                    </button></li>

                <!-- Cart Panel -->
                <div id="cart-panel" class="cart-panel">
                    <button onclick="toggleCart()" class="close-cart-btn">x</button>
                    <div class="container5">
                        <div class="cart-header">Your Cart:</div>
                        <div id="cart-items">
                            <!-- Display items in cart -->
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item" data-item-id="<?php echo $item['MenuItemID']; ?>">
                                    <div class="item-name"><?php echo htmlspecialchars($item['Name']); ?></div>
                                    <div class="item-details">
                                        <span class="item-price">Price:
                                            $<?php echo number_format($item['Price'], 2); ?></span>
                                        <span class="item-quantity">
                                            Quantity: <input type="number" value="<?php echo $item['Quantity']; ?>" min="1"
                                                class="quantity-input">
                                        </span>
                                        <button class="update-btn">Update</button>
                                        <button class="remove-btn"
                                            data-item-id="<?php echo $item['MenuItemID']; ?>">Remove</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="cart-footer">
                            <div id="total-price">Total: $<?php echo number_format($totalPrice, 2); ?></div>
                            <a href="index.php" class="shopping-link">
                                <button class="continue-shopping-btn">Continue Shopping</button>
                            </a>
                            <?php if ($totalPrice > 0): ?>
                                <a href="checkout.php" class="checkout-link">
                                    <button class="checkout-btn">Proceed to Checkout</button>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </ul>
        </div>

        <div class="clearfix"></div>

    </section>
    <!-- Navbar Section Ends Here -->


    <!-- Food Search Section Starts Here -->
    <section class="food-search text-center">
        <div class="container-search">
            <form action="food-search.php" method="POST">
                <input type="search" name="search" placeholder="Search for Food.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
    </section>
    <!-- Food Search Section Ends Here -->

    <!-- Slider Advertisement Starts Here -->
    <main class="main-content">
        <h1>Dazzling Donut in Town</h1>
        <div class="best-donuts-section">
            <div id="slider-container">
                <button type="button" class="slide-btn left-btn"><</button>
                <div id="slider">
                    <div class="slide">
                        <img src="images/fullofdonut12.webp" alt="fulldonut" class="img-responsive img-curve">
                    </div>
                    <div class="slide">
                        <img src="images/fullofdonut9.webp" alt="fulldonut" class="img-responsive img-curve">
                    </div>
                    <div class="slide">
                        <img src="images/fullofdonut11.webp" alt="fulldonut" class="img-responsive img-curve">
                    </div>
                    <div class="slide">
                        <img src="images/fullofdonut7.webp" alt="fulldonut" class="img-responsive img-curve">
                    </div>
                    <div div class="slide">
                        <img src="images/fullofdonut14.jpeg" alt="fulldonut" class="img-responsive img-curve">
                    </div>
                </div>

                <button type="button" class="slide-btn right-btn">></button>
            </div>
        </div>
    </main>
    <!-- Slider Advertisement Ends Here -->

    <!-- Section: Top Flavors -->
    <section>
        <h2 class="top-flavors">Our Top Flavors</h2>
        <div class="container1">
            <?php foreach ($topFlavors as $flavor): ?>
                <a href="food-search.php?search=<?= urlencode($flavor['Name']); ?>">
                    <div class="box-4">
                        <img src="<?= $flavor['Image']; ?>" alt="<?= $flavor['Name']; ?>" class="img-responsive img-curve">
                        <h3 class="float-text"><?= $flavor['Name']; ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- Section: Top Flavors Here -->

    <!-- Categories Section Starts Here -->
    <section class="categories">
        <h2 class="explore">Explore Foods</h2>
        <div class="container1">
            <a href="category.php?category=Donuts">
                <div class="box-4">
                    <img src="images/dazzlingchoco.webp" alt="DazzlingChoco" class="img-responsive img-curve">
                    <h3 class="float-text1">Donut</h3>
                </div>
            </a>

            <a href="category.php?category=Ice%20Cream">
                <div class="box-4">
                    <img src="images/chocolateicecream.webp" alt="Ice Cream" class="img-responsive img-curve">
                    <h3 class="float-text1">Ice Cream</h3>
                </div>
            </a>

            <a href="category.php?category=Drinks">
                <div class="box-4">
                    <img src="images/chocolatesmoothie.webp" alt="ChocoSmoothie" class="img-responsive img-curve">
                    <h3 class="float-text1">Drinks</h3>
                </div>
            </a>

            <a href="category.php?category=Merchandise">
                <div class="box-4">
                    <img src="images/totebag5.webp" alt="Totebag5" class="img-responsive img-curve">
                    <h3 class="float-text1">Merchandise</h3>
                </div>
            </a>

            <div class="clearfix"></div>
        </div>
    </section>
    <!-- Categories Section Ends Here -->

    <!-- Food Menu Section Starts Here -->
    <section class="food-menu1">
        <div class="container2">
            <h2 class="text-center">Food Menu</h2>
            <?php foreach ($limitedMenuItems as $item): ?>
                <div class="food-menu-box">
                    <div class="food-menu-img">
                        <img src="<?php echo $item['Image']; ?>"
                            alt="<?php echo htmlspecialchars($item['Name'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="img-responsive img-curve">
                    </div>
                    <div class="food-menu-desc">
                        <h4><?php echo htmlspecialchars($item['Name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="food-price">$<?php echo htmlspecialchars($item['Price'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="food-detail"><?php echo htmlspecialchars($item['Description'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <?php if ($item['Quantity'] > 0): ?>
                            <div class="food-quantity">Quantity Available: <?php echo $item['Quantity']; ?></div>
                            <button onclick="addToCart(<?php echo $item['MenuItemID']; ?>)" class="btn btn-primary">Add to
                                Cart</button>
                        <?php else: ?>
                            <p class="food-quantity">Not Available</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center">
            <a href="menu.php" class="see-all-btn">See All Foods</a>
        </div>
    </section>
    <!-- Food Menu Section Ends Here -->

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