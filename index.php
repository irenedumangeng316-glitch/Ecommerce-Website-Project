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
  <!-- Slick slider CSS -->
  <link rel="stylesheet" type="text/css" href="slick.css">
  <!-- Default CSS -->
  <link rel="stylesheet" href="main.css">
  <title>Chalicet - Home Page</title>
</head>
<body>
<?php include 'header.php'; ?>

<!-- Hero Slider -->
<div class="container-fluid">
  <div class="hero-slider">
    <div class="slider-item">
      <img src="img/Chalicet logo.png" alt="Chalicet Logo">
      <div class="slider-caption">
        <span>Test The Quality</span>
        <h1>Premium Tumbler</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.<br> Omnis pariatur voluptas ducimus!</p>
        <a href="shop.php" class="btn">Shop Now</a>
      </div>
    </div>
    <div class="slider-item">
      <img src="img/slider2.jpg" alt="Premium Tumbler">
      <div class="slider-caption">
        <span>Test The Quality</span>
        <h1>Premium Tumbler</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.<br> Omnis pariatur voluptas ducimus!</p>
        <a href="shop.php" class="btn">Shop Now</a>
      </div>
    </div>
  </div>
  <div class="control">
    <i class="bi bi-chevron-left prev"></i>
    <i class="bi bi-chevron-right next"></i>
  </div>
</div>

<div class="line"></div>

<!-- Services -->
<div class="services">
  <div class="row">
    <div class="box">
      <img src="img/Chalicet logo.png" alt="Free Shipping">
      <div>
        <h1>Free Shipping Fast!</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
      </div>
    </div>
    <div class="box">
      <img src="img/Chalicet logo.png" alt="Money Back Guarantee">
      <div>
        <h1>Money Back Guarantee!</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
      </div>
    </div>
    <div class="box">
      <img src="img/Chalicet logo.png" alt="Online Support">
      <div>
        <h1>Online Support 24/7!</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
      </div>
    </div>
  </div>
</div>

<div class="line2"></div>

<!-- Story -->
<div class="story">
  <div class="row">
    <div class="box">
      <span>Our Story</span>
      <h1>Start of an Artist</h1>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio dolor facilis beatae, deserunt rerum fugit, laboriosam velit sequi vitae molestias placeat eius aspernatur unde debitis nemo expedita earum magni fugiat.</p>
      <a href="shop.php" class="btn">Shop Now</a>
    </div>
    <div class="box">
      <img src="img/Chalicet logo.png" alt="Our Story">
    </div>
  </div>
</div>

<div class="line3"></div>

<!-- Testimonials -->
<div class="line4"></div>
<div class="testimonial-fluid">
  <h1 class="title">What Our Customers Say</h1>
  <div class="testimonial-slider">
    <div class="testimonial-item">
      <img src="img/Chalicet logo.png" alt="Customer Review">
      <div class="testimonial-caption">
        <span>Test The Quality</span>
        <h1>Premium Quality</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae inventore voluptate debitis sed magni ut dolore!</p>
      </div>
    </div>
    <!-- Repeat testimonial items as needed -->
  </div>
  <div class="control">
    <i class="bi bi-chevron-left prev1"></i>
    <i class="bi bi-chevron-right next1"></i>
  </div>
</div>

<div class="line"></div>

<!-- Discover -->
<div class="line2"></div>
<div class="discover">
  <div class="detail">
    <h1 class="title">Choose Your Own Design</h1>
    <span>Get Your Desired Design at 20% Off!</span>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta iure repellat suscipit magnam fugit ullam aliquid!</p>
    <a href="shop.php" class="btn">Discover Now</a>
  </div>
  <div class="img-box">
    <img src="img/Chalicet logo.png" alt="Discover Design">
  </div>
</div>

<div class="line3"></div>

<?php include 'homeshop.php'; ?>

<div class="line2"></div>

<!-- Newsletter -->
<div class="line2"></div>
<div class="newsletter">
  <h1 class="title">Join Our Newsletter</h1>
  <p>Get 30% off your next order. Be the first to learn about our promotions,
     special events, new designs and more.</p>
  <form method="post" action="subscribe.php" class="newsletter-form">
    <input type="email" name="email" placeholder="Your Email Address..." required>
    <button type="submit" class="btn">Subscribe Now</button>
  </form>
</div>

<div class="line3"></div>

<!-- Clients -->
<div class="client">
  <div class="box"><img src="img/Chalicet logo.png" alt="Client Logo"></div>
  <div class="box"><img src="img/Chalicet logo.png" alt="Client Logo"></div>
  <div class="box"><img src="img/Chalicet logo.png" alt="Client Logo"></div>
  <div class="box"><img src="img/Chalicet logo.png" alt="Client Logo"></div>
  <div class="box"><img src="img/Chalicet logo.png" alt="Client Logo"></div>
</div>

<?php include 'footer.php'; ?>

<!-- Scripts -->
<script src="jquery.js"></script>
<script src="slick.js"></script>
<script src="script2.js"></script>
</body>
</html>