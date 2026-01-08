<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" type="text/css" href="style.css">
	<script src="https://kit.fontawesome.com/f59eee459e.js"></script>
    <title>Admin Dashboard</title>
</head>
<body>
    <header class="header">
        <div class="flex">
            <!-- Logo -->
            <a href="admin_panel.php" class="logo">
                <img src="chalicet logo.png" alt="Logo">
            </a>

            <!-- Navigation -->
            <nav class="navbar">
                <a href="admin_panel.php">Home</a>
                <a href="admin_products.php">Products</a>
                <a href="admin_order.php">Orders</a>
                <a href="admin_user.php">Users</a>
                <a href="admin_message.php">Messages</a>
            </nav>

            <!-- Icons -->
            <div class="icons">
                <i class="fa-solid fa-user" id="user-btn"></i>
                <i class="fa-solid fa-list" id="menu-btn"></i>
            </div>

            <!-- User Box -->
            <div class="user-box"> 
                <p>Username: <span><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span></p>
                <p>Email: <span><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span></p>
                <form method="post">
                    <button type="submit" name="logout" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Banner -->
    <div class="banner">
        <div class="detail">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>! 
               Manage products, orders, users, and messages from here.</p>
        </div>
    </div>

    <div class="line"></div>
</body>
</html>