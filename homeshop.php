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
<section class="popular-brands">
  <h2>POPULAR DESIGNS</h2>
  <div class="controls">
    <i class="bi bi-chevron-left left"></i>
    <i class="bi bi-chevron-right right"></i>
  </div>

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

  <div class="popular-brands-content">
    <?php
    $stmt = $conn->prepare("SELECT * FROM products");
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

<!-- Scripts -->
<script src="jquery.js"></script>
<script src="slick.js"></script>
<script type="text/javascript">
  $('.popular-brands-content').slick({
    lazyLoad: 'ondemand',
    slidesToShow: 4,
    slidesToScroll: 1,
    prevArrow: $('.left'),
    nextArrow: $('.right'),
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
</script>
</body>
</html>