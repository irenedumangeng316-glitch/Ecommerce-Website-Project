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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap icon link -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="main.css">
  <title>Chalicet - Orders Page</title>
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
  <div class="detail">
    <h1>Orders</h1>
    <p>Track your placed orders and payment status below.</p>
    <a href="index.php">Home</a><span>/ Orders</span>
  </div>
</div>

<div class="line"></div>

<div class="order-section">
  <div class="box-container">
    <?php
    // Secure prepared statement
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY placed_on DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($fetch_orders = $result->fetch_assoc()) {
            ?>
            <div class="box">
              <p>Placed on: <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
              <p>Name: <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
              <p>Number: <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
              <p>Email: <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
              <p>Address: <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
              <p>Payment Method: <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
              <p>Your Order: <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
              <p>Total Price: <span>$<?= number_format($fetch_orders['total_price'], 2); ?></span></p>
              <p>Payment Status: 
                <span class="<?= $fetch_orders['payment_status'] === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                  <?= htmlspecialchars($fetch_orders['payment_status']); ?>
                </span>
              </p>
            </div>
            <?php
        }
    } else {
        echo '<div class="empty"><p>No orders placed yet!</p></div>';
    }
    ?>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>