# Dazzling Donut - Online Food Ordering System

## Overview

This project is developed for the **COMP1044: Database and Interfaces** coursework. The system simulates an **online food ordering application** for a restaurant named **Dazzling Donut**. It enables customers to register, browse menus, add items to cart, place orders, and simulate checkout with basic payment options.

---

## Technologies Used

* **Frontend**: HTML5, CSS3, JavaScript
* **Backend**: PHP
* **Database**: MySQL

---

## Features Implemented

### User Account Management

* User registration, login/logout
* Profile edit feature

### Menu Browsing

* Menu items categorized (Donuts, Ice Cream, Drinks, Merchandise)
* Display: name, image, description, price, availability

### Cart System

* Add to cart with quantity
* Update/remove items in cart
* Real-time price calculation

### Checkout Process

* Collects name, phone number, address
* Displays order summary
* Simulated payment options

### Admin Panel

* Add/edit/delete menu items and categories (manually or via database)

---

## Project Structure

```
COMP1044_SRC/
|
|-- css/
|-- images/
|-- addToCart.php
|-- backToHomepage.php
|-- category.php
|-- checkout.php
|-- connection.php
|-- contact.php
|-- customerDetails.php
|-- customerPayment.php
|-- editprofile.php
|-- food-search.php
|-- functions.php
|-- index.php
|-- login.php
|-- logout.php
|-- menu.php
|-- orderHistory.php
|-- orderonline.php
|-- orderSummary.php
|-- overallSummary.php
|-- removeFromCart.php
|-- saveCustomerDetails.php
|-- saveCustomerPayment.php
|-- signup.php
|-- updateCartQuantity.php
|-- COMP1044_database.sql
|-- README.md
```

---

## 🛠How to Setup & Run the Project

### 1️⃣ Install XAMPP

* Download XAMPP from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)
* Install and run **Apache** and **MySQL** from XAMPP Control Panel

### 2️⃣ Place Project Files

* Extract `COMP1044_SRC.zip`
* Move the entire folder to: `C:\xampp\htdocs\`

  * Example: `C:\xampp\htdocs\COMP1044_SRC\`

### 3️⃣ Import SQL File into phpMyAdmin

1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click "New" to create a new database
3. Name it: `comp1044_database`
4. Click the database name on the left
5. Go to **Import** tab
6. Choose file: `COMP1044_database.sql`
7. Click **Go** to import

### 4️⃣ Launch the Web App

* Open browser and go to:

```
http://localhost/COMP1044_SRC/index.php
```

---

## Notes

* Ensure form validations are in place
* Follow secure PHP practices (e.g., prepared statements)
* Comment code where necessary
* Keep UI consistent and mobile-friendly where possible

---

© University of Nottingham Malaysia — COMP1044 Coursework 2024
