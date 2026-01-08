<?php
@include 'connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}

// Wishlist count
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_num_rows = $result->fetch_assoc()['total'] ?? 0;
$stmt->close();

// Cart count
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM cart WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_num_rows = $result->fetch_assoc()['total'] ?? 0;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap icon link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
    <title>Chalicet - Header</title>
</head>
<body>
<header class="header">
    <div class="flex">
        <!-- Logo -->
        <a href="admin_panel.php" class="logo">
            <img src="img/logo.png" alt="Chalicet Logo">
        </a>

        <!-- Navigation -->
        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="shop.php">Shop</a>
            <a href="order.php">Order</a>
            <a href="contact.php">Contact</a>
        </nav>

        <!-- Icons -->
        <div class="icons">
            <i class="bi bi-person" id="user-btn" aria-label="User"></i>
            <a href="wishlist.php" aria-label="Wishlist">
                <i class="bi bi-heart"></i><sup><?= $wishlist_num_rows; ?></sup>
            </a>
            <a href="cart.php" aria-label="Cart">
                <i class="bi bi-cart"></i><sup><?= $cart_num_rows; ?></sup>
            </a>
            <i class="bi bi-list" id="menu-btn" aria-label="Menu"></i>
        </div>

        <!-- User Box -->
        <div class="user-box">
            <p>Username: <span><?= htmlspecialchars($_SESSION['user_name']); ?></span></p>
            <p>Email: <span><?= htmlspecialchars($_SESSION['user_email']); ?></span></p>
            <form method="post">
                <button type="submit" name="logout" class="logout-btn">Log Out</button>
            </form>
        </div>
    </div>
</header>
</body>
</html>