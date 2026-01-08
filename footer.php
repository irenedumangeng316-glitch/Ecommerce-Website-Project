<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Footer</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="main.css">
</head>
<body>
  <div class="line4"></div>
  <footer>
    <div class="inner-footer">
      
      <!-- About Us -->
      <div class="card">
        <h3>About Us</h3>
        <ul>
          <li><a href="about.php">About Us</a></li>
          <li><a href="#">Our Difference</a></li>
          <li><a href="#">Community Matters</a></li>
          <li><a href="#">Press</a></li>
          <li><a href="#">Bouqs Video</a></li>
          <li><a href="blog.php">Blog</a></li>
        </ul>
      </div>

      <!-- Services -->
      <div class="card">
        <h3>Services</h3>
        <ul>
          <li><a href="orders.php">Orders</a></li>
          <li><a href="help.php">Help Center</a></li>
          <li><a href="shipping.php">Shipping</a></li>
          <li><a href="terms.php">Terms of Use</a></li>
          <li><a href="account.php">Account Detail</a></li>
          <li><a href="myaccount.php">My Account</a></li>
        </ul>
      </div>

      <!-- Local -->
      <div class="card">
        <h3>Local</h3>
        <ul>
          <li>Benguet</li>
          <li>Ifugao</li>
          <li>Mt Province</li>
          <li>Kalinga</li>
          <li>Abra</li>
          <li>Apayao</li>
        </ul>
      </div>

      <!-- Newsletter -->
      <div class="card">
        <h3>Newsletter</h3>
        <p>Sign up for the latest offers and exclusives</p>
        <form class="newsletter-form" method="post" action="subscribe.php">
          <div class="input-field">
            <input type="email" name="email" placeholder="Enter your email..." required>
            <button type="submit"><i class="bi bi-envelope"></i></button>
          </div>
        </form>
        <div class="social-links">
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>

    </div>

    <!-- Bottom Footer -->
    <div class="bottom-footer">
      <p>&copy; <span id="year"></span> All Rights Reserved</p>
    </div>
  </footer>

  <script>
    // Auto-update year
    document.getElementById("year").textContent = new Date().getFullYear();
  </script>
</body>
</html>