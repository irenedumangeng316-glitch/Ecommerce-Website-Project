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

$message = [];

// Update quantity
if (isset($_POST['update_cart'])) {
    $cart_id   = intval($_POST['cart_id']);
    $quantity  = max(1, intval($_POST['product_quantity']));

    $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    $stmt->execute();
    $stmt->close();

    $message[] = 'Cart updated successfully';
}

// Delete single product
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('location:cart.php');
    exit();
}

// Delete all products
if (isset($_GET['delete_all'])) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header('location:cart.php');
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
  <title>Chalicet - Cart</title>
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
  <div class="detail">
    <h1>My Cart</h1>
    <p>Review and manage products you’ve added to your cart.</p>
    <a href="index.php">Home</a><span>/ Cart</span>
  </div>
</div>

<div class="line"></div>

<section class="shop">
  <h1 class="title">Products in Your Cart</h1>

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

  <div class="box-container">
    <?php
    $grand_total = 0;
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($fetch_cart = $result->fetch_assoc()) {
            $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
            $grand_total += $sub_total;
            ?>
            <form method="post" class="box">
              <img src="image/<?= htmlspecialchars($fetch_cart['image']); ?>" 
                   alt="<?= htmlspecialchars($fetch_cart['name']); ?>">
              <div class="price">$<?= number_format($fetch_cart['price'], 2); ?>/-</div>
              <div class="name"><?= htmlspecialchars($fetch_cart['name']); ?></div>
              <div class="quantity-box">
                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                <input type="number" name="product_quantity" value="<?= $fetch_cart['quantity']; ?>" min="1" class="quantity">
                <button type="submit" name="update_cart" class="btn">Update</button>
              </div>
              <div class="sub-total">Subtotal: $<?= number_format($sub_total, 2); ?>/-</div>
              <div class="icon">
                <a href="view_page.php?pid=<?= $fetch_cart['pid']; ?>" class="bi bi-eye-fill" aria-label="View"></a>
                <a href="cart.php?delete=<?= $fetch_cart['id']; ?>" class="bi bi-x" 
                   onclick="return confirm('Are you sure you want to delete this product from your cart?');" aria-label="Delete"></a>
              </div>
            </form>
            <?php
        }
    } else {
        echo '<p class="empty">Your cart is empty!</p>';
    }
    ?>
  </div>

  <div class="cart_total">
    <p>Grand Total : <span>$<?= number_format($grand_total, 2); ?>/-</span></p>
    <a href="shop.php" class="btn">Continue Shopping</a>
    <?php if ($grand_total > 0): ?>
      <a href="cart.php?delete_all" class="btn2" 
         onclick="return confirm('Are you sure you want to delete all products from your cart?');">Delete All</a>
      <a href="checkout.php" class="btn">Proceed to Checkout</a>
    <?php else: ?>
      <button class="btn2" disabled>Delete All</button>
      <button class="btn" disabled>Proceed to Checkout</button>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>