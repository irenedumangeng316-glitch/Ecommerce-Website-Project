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

// Add to wishlist
if (isset($_POST['add_to_wishlist'])) {
    $product_id    = $_POST['product_id'];
    $product_name  = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE name=? AND user_id=?");
    $stmt->bind_param("si", $product_name, $user_id);
    $stmt->execute();
    $wishlist_number = $stmt->get_result();
    $stmt->close();

    $stmt = $conn->prepare("SELECT id FROM cart WHERE name=? AND user_id=?");
    $stmt->bind_param("si", $product_name, $user_id);
    $stmt->execute();
    $cart_num = $stmt->get_result();
    $stmt->close();

    if ($wishlist_number->num_rows > 0) {
        $message[] = 'Product already exists in Wishlist';
    } elseif ($cart_num->num_rows > 0) {
        $message[] = 'Product already exists in Cart';
    } else {
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisds", $user_id, $product_id, $product_name, $product_price, $product_image);
        $stmt->execute();
        $stmt->close();
        $message[] = 'Product successfully added to your Wishlist';
    }
}

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id    = $_POST['product_id'];
    $product_name  = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

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
  <title>Chalicet - Shop Page</title>
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
  <div class="detail">
    <h1>Our Shop</h1>
    <p>Browse our best sellers and discover premium designs.</p>
    <a href="index.php">Home</a><span>/ Shop</span>
  </div>
</div>

<div class="line"></div>

<section class="shop">
  <h1 class="title">Our Shop's Best Sellers</h1>

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
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($fetch_products = $result->fetch_assoc()) {
            ?>
            <form method="post" class="box">
              <img src="image/<?= htmlspecialchars($fetch_products['image']); ?>" 
                   alt="<?= htmlspecialchars($fetch_products['name']); ?>">
              <div class="price">$<?= number_format($fetch_products['price'], 2); ?>/-</div>
              <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
              <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
              <input type="hidden" name="product_name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
              <input type="hidden" name="product_price" value="<?= $fetch_products['price']; ?>">
              <input type="hidden" name="product_quantity" value="1">
              <input type="hidden" name="product_image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
              <div class="icon">
                <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="bi bi-eye-fill" aria-label="View"></a>
                <button type="submit" name="add_to_wishlist" class="bi bi-heart" aria-label="Add to Wishlist"></button>
                <button type="submit" name="add_to_cart" class="bi bi-cart" aria-label="Add to Cart"></button>
              </div>
            </form>
            <?php
        }
    } else {
        echo '<p class="empty">No Products Added Yet!</p>';
    }
    ?>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>