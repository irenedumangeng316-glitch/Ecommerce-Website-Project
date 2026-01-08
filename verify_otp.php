<?php
session_start();

if (isset($_POST['verify-btn'])) {
    $entered_otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp'] && time() < $_SESSION['otp_expiry']) {
        // OTP valid → finalize login
        $user = $_SESSION['pending_user'];

        if ($user['user_type'] == 'admin') {
            $_SESSION['admin_name']  = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_id']    = $user['id'];
            header('Location: admin_panel.php');
            exit();
        } else {
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_id']    = $user['id'];
            header('Location: index.php');
            exit();
        }
    } else {
        $error = "Invalid or expired OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
</head>
<body>
    <?php if (!empty($error)) : ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <h1>Enter OTP</h1>
        <input type="text" name="otp" placeholder="Enter the 6-digit OTP" required><br>
        <input type="submit" name="verify-btn" value="Verify OTP">
    </form>
</body>
</html>