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

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id    = $_POST['product_id'];
    $product_name  = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

    $stmt = $conn->prepare("SELECT id FROM cart WHERE name=? AND user_id=?");
    $stmt->bind_param("si", $product_name, $user_id);
    $stmt->execute();
    $cart_num = $stmt->get_result();
    $stmt->close();

    if ($cart_num->num_rows > 0) {
        $message[] = 'Product already exists in Cart';
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdis", $user_id, $product_id, $product_name, $product_price, $product_quantity, $product_image);
        $stmt->execute();
        $stmt->close();
        $message[] = 'Product successfully added to your Cart';
    }
}

// Delete product from wishlist
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('location:wishlist.php');
    exit();
}

// Delete all products from wishlist
if (isset($_GET['delete_all'])) {
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header('location:wishlist.php');
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
  <title>Chalicet - Wishlist</title>
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
  <div class="detail">
    <h1>My Wishlist</h1>
    <p>Review and manage products you’ve saved for later.</p>
    <a href="index.php">Home</a><span>/ Wishlist</span>
  </div>
</div>

<div class="line"></div>

<section class="shop">
  <h1 class="title">Products Added in Wishlist</h1>

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
    $stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($fetch_wishlist = $result->fetch_assoc()) {
            ?>
            <form method="post" class="box">
              <img src="image/<?= htmlspecialchars($fetch_wishlist['image']); ?>" 
                   alt="<?= htmlspecialchars($fetch_wishlist['name']); ?>">
              <div class="price">$<?= number_format($fetch_wishlist['price'], 2); ?>/-</div>
              <div class="name"><?= htmlspecialchars($fetch_wishlist['name']); ?></div>
              <input type="hidden" name="product_id" value="<?= $fetch_wishlist['pid']; ?>">
              <input type="hidden" name="product_name" value="<?= htmlspecialchars($fetch_wishlist['name']); ?>">
              <input type="hidden" name="product_price" value="<?= $fetch_wishlist['price']; ?>">
              <input type="hidden" name="product_image" value="<?= htmlspecialchars($fetch_wishlist['image']); ?>">
              <div class="icon">
                <a href="view_page.php?pid=<?= $fetch_wishlist['pid']; ?>" class="bi bi-eye-fill" aria-label="View"></a>
                <a href="wishlist.php?delete=<?= $fetch_wishlist['id']; ?>" class="bi bi-x" 
                   onclick="return confirm('Are you sure you want to delete this product from your wishlist?');" aria-label="Delete"></a>
                <button type="submit" name="add_to_cart" class="bi bi-cart" aria-label="Add to Cart"></button>
              </div>
            </form>
            <?php
            $grand_total += $fetch_wishlist['price'];
        }
    } else {
        echo '<p class="empty">No Products Added Yet!</p>';
    }
    ?>
  </div>

  <div class="wishlist_total">
    <p>Grand Total : <span>$<?= number_format($grand_total, 2); ?>/-</span></p>
    <a href="shop.php" class="btn">Continue Shopping</a>
    <?php if ($grand_total > 0): ?>
      <a href="wishlist.php?delete_all" class="btn2" 
         onclick="return confirm('Are you sure you want to delete all products from your wishlist?');">Delete All</a>
    <?php else: ?>
      <button class="btn2" disabled>Delete All</button>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>