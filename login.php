<?php
include 'connection.php';
session_start();

if (isset($_POST['submit-btn'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Check user
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if ($row) {
        if (password_verify($password, $row['password'])) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + 300; // 5 minutes
            $_SESSION['pending_user'] = $row;

            // Send OTP via email
            $subject = "Your OTP Code";
            $message = "Hello " . $row['name'] . ",\n\nYour OTP is: $otp\nIt will expire in 5 minutes.";
            $headers = "From: no-reply@yourdomain.com";

            if (mail($row['email'], $subject, $message, $headers)) {
                header('Location: verify_otp.php');
                exit();
            } else {
                $error = "Failed to send OTP email. Please try again.";
            }
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php if (!empty($error)) : ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <h1>Login</h1>
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <input type="password" name="password" placeholder="Enter your password" required><br>
        <input type="submit" name="submit-btn" value="Login">
    </form>
</body>
</html>