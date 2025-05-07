CREATE DATABASE IF NOT EXISTS COMP1044_database;

-- DROP TABLE IF EXISTS Users;
CREATE TABLE IF NOT EXISTS COMP1044_database.Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255),
    Email VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS COMP1044_database.Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS COMP1044_database.MenuItems (
    MenuItemID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryID INT,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Image VARCHAR(255),
    Price DECIMAL(6,2) NOT NULL,
    Quantity INT NOT NULL, -- Changed from 'Availability BOOLEAN NOT NULL' to 'Quantity INT NOT NULL'
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID)
);

CREATE TABLE IF NOT EXISTS COMP1044_database.Carts (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS COMP1044_database.CartItems (
    CartItemID INT AUTO_INCREMENT PRIMARY KEY,
    CartID INT,
    MenuItemID INT,
    Quantity INT NOT NULL,
    FOREIGN KEY (CartID) REFERENCES Carts(CartID),
    FOREIGN KEY (MenuItemID) REFERENCES MenuItems(MenuItemID)
);

CREATE TABLE IF NOT EXISTS COMP1044_database.Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    CartID INT,
    OrderDate DATETIME NOT NULL,
    TotalPrice DECIMAL(8,2) NOT NULL,
    Status VARCHAR(20) DEFAULT 'Active',
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE IF NOT EXISTS COMP1044_database.OrderDetails (
    OrderDetailID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    CartID INT,
    MenuItemID INT,
    Quantity INT NOT NULL,
    ItemPrice DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
    FOREIGN KEY (MenuItemID) REFERENCES MenuItems(MenuItemID)
);

CREATE TABLE IF NOT EXISTS COMP1044_database.ShippingDetails (
    ShippingID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    RecipientName VARCHAR(255) NOT NULL,
    AddressLine1 VARCHAR(255) NOT NULL,
    AddressLine2 VARCHAR(255),
    City VARCHAR(100) NOT NULL,
    State VARCHAR(100) NOT NULL,
    PostalCode VARCHAR(20) NOT NULL,
    Country VARCHAR(100) NOT NULL,
    ContactNumber VARCHAR(20) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);

CREATE TABLE IF NOT EXISTS COMP1044_database.Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    CartID INT,
    OrderID INT,
    PaymentMethod VARCHAR(50),
    PaymentStatus VARCHAR(50),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);

INSERT INTO COMP1044_database.Categories (Name)
VALUES ('Ice Cream'), ('Donuts'), ('Merchandise'), ('Drinks');


INSERT INTO COMP1044_database.MenuItems (CategoryID, Name, Description, Image, Price, Quantity)
VALUES 
(1, 'Vanilla Ice Cream', 'Classic Vanilla Ice Cream with real vanilla beans for a creamy, indulgent treat.', 'images/vanillaicecream.webp', 5.99, 20),
(1, 'Chocolate Ice Cream', 'Decadent Chocolate Ice Cream made from fine cocoa and fresh cream for chocolate lovers.', 'images/chocolateicecream.webp', 5.99, 20),
(1, 'Coffee Ice Cream', 'Rich Coffee Ice Cream made with premium beans and creamy milk for coffee enthusiasts.', 'images/coffeeicecream.webp', 5.99, 10),
(1, 'Matcha Ice Cream', 'Refreshing Matcha Ice Cream crafted with authentic Japanese matcha for a delicate flavor.', 'images/matchaicecream.webp', 5.99, 5),
(1, 'Strawberry Ice Cream', 'Sweet Strawberry Ice Cream, bursting with fresh strawberries for a refreshing summer taste.', 'images/strawberryicecream.webp', 5.99, 3);

INSERT INTO COMP1044_database.MenuItems (CategoryID, Name, Description, Image, Price, Quantity)
VALUES
(2, 'Plain Chocolate ', 'Indulge in the irresistible combination of rich chocolate icing topped with crunchy Oreo cookies.', 'images/plainchoco.webp', 4.99, 10),
(2, 'Peanut', 'Enjoy the delightful blend of savory peanuts and sweet maple syrup atop our fluffy donut.', 'images/peanut.webp', 4.99, 10),
(2, 'Plain', 'Treat yourself to the simple yet satisfying flavor of our classic plain donut, topped with a drizzle of maple syrup.', 'images/plain.webp', 4.99, 30),
(2, 'Plain Strawberry', 'Delight in the freshness of ripe strawberries paired perfectly with our soft, pillowy donut base.', 'images/plainstrawberry.webp', 5.50, 10),
(2, 'Coffee', 'Savor the rich and bold flavor of coffee infused into every bite of our delectable donut, topped with a smooth coffee glaze.', 'images/coffee.webp', 5.99, 20),

(2, 'Dazzling Chocolate ', 'A divine creation featuring a rich chocolate glaze and a sprinkle of magic.', 'images/dazzlingchoco.webp', 6.50, 2),
(2, 'Milk Chocolate' , 'Indulge in the velvety smoothness of our milk chocolate glazed donut, a delightful treat topped with a drizzle of maple syrup.', 'images/milkchoco.webp', 6.50, 10),
(2, 'Oreo Chocolate', 'Savor our Oreo Chocolate Donut, with rich chocolate icing, crumbled Oreo cookies, and maple syrup.', 'images/oreochoco.webp', 6.50, 15),
(2, 'Peanut Chocolate ', 'Enjoy our Peanut Chocolate Donut, blending savory peanuts, sweet chocolate glaze, and a hint of maple syrup.', 'images/peanutchoco.webp', 6.50, 12),
(2, 'Strawberry Chocolate ', 'Savor the delightful blend of sweet strawberries and rich chocolate in our strawberry chocolate donut, finished with a maple syrup drizzle.', 'images/strawberrychoco.webp', 5.50, 18),

(2, 'Cream Donut', 'Indulge in the delightful fusion of creamy ice cream and rich chocolate glaze with our Cream Donut.', 'images/icrecream.webp', 6.50, 19),
(2, 'Rainbow Donut', 'Embark on a colorful and flavorful journey with our Rainbow Donut, featuring vibrant rainbow glaze.', 'images/rainbow.webp', 6.50, 13),
(2, 'Strawberry Cream Cheese ', 'Savor the exquisite blend of creamy cream cheese and fresh strawberries in our Strawberry Cream Cheese Donut.', 'images/strawberry.webp', 6.50, 8);


INSERT INTO COMP1044_database.MenuItems (CategoryID, Name, Description, Image, Price, Quantity)
VALUES
(3, 'Phone Case1', 'Elevate your style with our Signature Phone Case. Crafted from premium materials, this case offers both protection and elegance.', 'images/phone case.webp', 10.99, 5),
(3, 'Phone Case2', 'Add glamour to your phone with our Phone Charm Case. Made from durable silicon, it combines style and protection effortlessly.', 'images/phonecase.webp', 10.99, 3),
(3, 'Totebag1', 'Make a statement with our Dazzling Rainbow Tote Bag. Vibrant colors inspired by donut toppings add a pop to any outfit.', 'images/totebag2.webp', 18.99, 10),
(3, 'Totebag2 ', 'Carry essentials in style with our Dazzling Donut Tote Bag. Featuring our iconic logo, it blends fashion and function seamlessly.', 'images/totebag3.webp', 18.99, 20),
(3, 'Totebag3', 'Embrace style and comfort with Dazzling''s Totebag, featuring a cozy cotton fabric adorned with a charming donut logo.', 'images/totebag4.webp', 18.99, 20),
(3, 'Totebag4 ', 'Elevate your look with our totebag, crafted from premium cotton material and adorned with a stylish donut logo.', 'images/totebag5.webp', 18.99, 10),
(3, 'Donut water bottle', 'Sip in style with our Dazzling Dream Tumbler. Crafted from stainless steel, it keeps beverages hot or cold for hours.', 'images/waterbottle.webp', 39.99, 10);

INSERT INTO COMP1044_database.MenuItems (CategoryID, Name, Description, Image, Price, Quantity)
VALUES
(4, 'Hot Coffee', 'Indulge in the bold and rich flavors of our signature hot espresso.', 'images/hotcoffee.webp', 6.99, 6),
(4, 'Hot Chocolate', 'Treat yourself to the comforting warmth of our Hot Chocolate, made with rich cocoa for a decadent indulgence.', 'images/chocolate.webp', 7.99, 4),
(4, 'Chocolate Smoothie', 'Cool off with our velvety chocolate smoothie, a delightful treat for any chocoholic.', 'images/chocolatesmoothie.webp', 7.99, 7),
(4, 'Hot Latte', 'Sip on the smooth and creamy goodness of our hot latte, crafted with care for a soothing coffee experience', 'images/hotlatte.webp', 6.99, 14),
(4, 'Hot Milk', 'Indulge in the creamy richness of our hot milk, a comforting beverage perfect for warming up on chilly days.', 'images/hotmilk.webp', 5.99, 21),
(4, 'Hot Rose Milk', 'Experience floral notes and creamy richness with hot rose milk, a unique twist on a classic favorite.', 'images/hotrosemilk.webp', 5.99, 15),
(4, 'Iced Matcha Latte', 'Experience the enchanting allure of matcha with our creamy matcha latte.', 'images/icedmatchalatte.webp', 7.99, 19),
(4, 'Iced Milk', 'Refresh yourself with a cold glass of our creamy and soothing iced milk.', 'images/icedmilk.webp', 5.99, 9),
(4, 'Hot Matcha Latte', 'Enjoy the earthy sweetness of our Hot Matcha Latte, a comforting drink to uplift your spirits', 'images/matchalatte.webp', 8.99, 7),
(4, 'Strawberry Milk Shake', 'Savor the taste of summer with our refreshing strawberry milkshake.', 'images/starwberrymilkshake.webp', 8.99, 4);
