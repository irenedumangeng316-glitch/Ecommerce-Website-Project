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
if (isset($_POST['Logout'])) {
    session_destroy();
    header('location:login.php');
    exit();
}

$message = [];

if (isset($_POST['submit-btn'])) {
    // Sanitize inputs
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $number  = trim($_POST['number']);
    $msgText = trim($_POST['message']);

    // Check if message already exists
    $stmt = $conn->prepare("SELECT id FROM message WHERE name=? AND email=? AND number=? AND message=? AND user_id=?");
    $stmt->bind_param("ssssi", $name, $email, $number, $msgText, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $message[] = "Message already sent!";
    } else {
        // Insert new message
        $stmt = $conn->prepare("INSERT INTO message (user_id, name, email, number, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $name, $email, $number, $msgText);
        $stmt->execute();
        $stmt->close();
        $message[] = "Message sent successfully!";
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
    <title>Chalicet - Contact Us Page</title>
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
    <div class="detail">
        <h1>Contact</h1>
        <p>We’d love to hear from you! Send us a message below.</p>
        <a href="index.php">Home</a><span>/ Contact</span>
    </div>
</div>
<div class="line"></div>

<!-- Services -->
<div class="services">
    <div class="row">
        <div class="box">
            <img src="img/Chalicet logo.png" alt="">
            <div>
                <h1>Free Shipping Fast!</h1>
                <p>Enjoy quick and reliable delivery on all orders.</p>
            </div>
        </div>
        <div class="box">
            <img src="img/Chalicet logo.png" alt="">
            <div>
                <h1>Money Back Guarantee!</h1>
                <p>Shop with confidence — we’ve got you covered.</p>
            </div>
        </div>
        <div class="box">
            <img src="img/Chalicet logo.png" alt="">
            <div>
                <h1>Online Support 24/7!</h1>
                <p>We’re here to help anytime, anywhere.</p>
            </div>
        </div>
    </div>
</div>

<div class="line4"></div>

<!-- Contact Form -->
<div class="form-container">
    <h1 class="title">Leave a Message</h1>

    <?php
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<div class="message"><span>'.$msg.'</span>
                  <i class="bi bi-x-circle" onclick="this.parentElement.remove();"></i></div>';
        }
    }
    ?>

    <form method="post">
        <div class="input-field">
            <label>Your Name</label><br>
            <input type="text" name="name" required>
        </div>
        <div class="input-field">
            <label>Your Email</label><br>
            <input type="email" name="email" required>
        </div>
        <div class="input-field">
            <label>Number</label><br>
            <input type="text" name="number" required>
        </div>
        <div class="input-field">
            <label>Your Message</label><br>
            <textarea name="message" required></textarea>
        </div>
        <button type="submit" name="submit-btn" class="btn">Send Message</button>
    </form>
</div>

<div class="line"></div>
<div class="line2"></div>

<!-- Contact Info -->
<div class="address">
    <h1 class="title">Our Contact</h1>
    <div class="row">
        <div class="box">
            <i class="bi bi-map-fill"></i>
            <div>
                <h4>Address</h4>
                <p>123 Main Street,<br>City, Country, Zip Code</p>
            </div>
        </div>
        <div class="box">
            <i class="bi bi-telephone-fill"></i>
            <div>
                <h4>Phone Number</h4>
                <p>+63 912 345 6789</p>
            </div>
        </div>
        <div class="box">
            <i class="bi bi-envelope-fill"></i>
            <div>
                <h4>Email</h4>
                <p>noemail@email.com</p>
            </div>
        </div>
    </div>
</div>

<div class="line3"></div>
<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>