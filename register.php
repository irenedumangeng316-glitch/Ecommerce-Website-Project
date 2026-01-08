<?php
include 'connection.php';
session_start();

if (isset($_POST['submit-btn'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password  = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    $errors = [];

    // --- Server-side validation ---
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    $uppercase    = preg_match('@[A-Z]@', $password);
    $lowercase    = preg_match('@[a-z]@', $password);
    $number       = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (strlen($password) < 6 || !$uppercase || !$lowercase || !$number || !$specialChars) {
        $errors[] = "Password must be at least 6 characters long, include at least 1 uppercase, 1 lowercase, 1 number, and 1 special character.";
    }

    if ($password !== $cpassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "User already exists with this email.";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO `users` (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);

        $_SESSION['success'] = "Registered successfully! You can now log in.";
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register page</title>
  <link rel="stylesheet" href="main.css">
</head>
<body>
<section class="form-container">
  <?php
  if (!empty($errors)) {
      foreach ($errors as $error) {
          echo "<div class='message error'><span>$error</span></div>";
      }
  }
  if (isset($_SESSION['success'])) {
      echo "<div class='message success'><span>{$_SESSION['success']}</span></div>";
      unset($_SESSION['success']);
  }
  ?>
  <form method="post" id="registerForm">
    <h1>Register Now</h1>
    <input type="text" name="name" id="name" placeholder="Enter your name" required>
    <input type="email" name="email" id="email" placeholder="Enter your email" required>
    <input type="password" name="password" id="password" placeholder="Enter your password" required>
    <input type="password" name="cpassword" id="cpassword" placeholder="Confirm your password" required>
    <input type="submit" name="submit-btn" value="Register Now" class="btn">
    <p>Already have an account? <a href="login.php">Login now</a></p>
  </form>
</section>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let errors = [];
    let name = document.getElementById('name').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value;
    let cpassword = document.getElementById('cpassword').value;

    // Name check
    if (name.length === 0) {
        errors.push("Name is required.");
    }

    // Email check
    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.match(emailPattern)) {
        errors.push("Invalid email format.");
    }

    // Password strength check
    let uppercase = /[A-Z]/.test(password);
    let lowercase = /[a-z]/.test(password);
    let number = /[0-9]/.test(password);
    let specialChar = /[^A-Za-z0-9]/.test(password);

    if (password.length < 6 || !uppercase || !lowercase || !number || !specialChar) {
        errors.push("Password must be at least 6 characters long, include at least 1 uppercase, 1 lowercase, 1 number, and 1 special character.");
    }

    // Confirm password
    if (password !== cpassword) {
        errors.push("Passwords do not match.");
    }

    if (errors.length > 0) {
        e.preventDefault(); // Stop form submission
        alert(errors.join("\n")); // Show errors in alert (you can style this better)
    }
});
</script>
</body>
</html>