<?php
@include 'connection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

$message = [];

// Place order
if (isset($_POST['place_order'])) {
    $name    = trim($_POST['name']);
    $number  = trim($_POST['number']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);
    $method  = $_POST['method'];

    // Fetch cart items
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $total_products = [];
        $grand_total = 0;

        while ($cart_item = $result->fetch_assoc()) {
            $total_products[] = $cart_item['name'].' ('.$cart_item['quantity'].')';
            $grand_total += $cart_item['price'] * $cart_item['quantity'];
        }

        $products_list = implode(', ', $total_products);

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, number, email, address, method, total_products, total_price, placed_on, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("isssssds", $user_id, $name, $number, $email, $address, $method, $products_list, $grand_total);
        $stmt->execute();
        $stmt->close();

        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $message[] = 'Order placed successfully!';
    } else {
        $message[] = 'Your cart is empty!';
    }
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
  <title>Chalicet - Checkout</title>
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
  <div class="detail">
    <h1>Checkout</h1>
    <p>Complete your order by filling in your details below.</p>
    <a href="index.php">Home</a><span>/ Checkout</span>
  </div>
</div>

<div class="line"></div>

<section class="checkout">
  <h1 class="title">Place Your Order</h1>

  <?php
  if (!empty($message)) {
      foreach ($message as $msg) {
          echo '<div class="message">
                  <span>'.htmlspecialchars($msg).'</span>
                  <i class="bi bi-x-circle" onclick="this.parentElement.remove();"></i>
                </div>';
      }
  }
  ?>

  <form method="post" class="checkout-form">
    <div class="input-box">
      <span>Name:</span>
      <input type="text" name="name" required>
    </div>
    <div class="input-box">
      <span>Number:</span>
      <input type="text" name="number" required>
    </div>
    <div class="input-box">
      <span>Email:</span>
      <input type="email" name="email" required>
    </div>
    <div class="input-box">
      <span>Address:</span>
      <textarea name="address" required></textarea>
    </div>
    <div class="input-box">
      <span>Payment Method:</span>
      <select name="method" required>
        <option value="cash on delivery">Cash on Delivery</option>
        <option value="credit card">Credit Card</option>
        <option value="paypal">PayPal</option>
      </select>
    </div>
    <input type="submit" name="place_order" value="Place Order" class="btn">
  </form>
</section>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>