<?php
include 'connection.php';
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Calculate totals securely
$total_pendings = 0;
$stmt = $conn->prepare("SELECT total_price FROM orders WHERE payment_status = 'pending'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $total_pendings += $row['total_price'];
}
$stmt->close();

$total_completes = 0;
$stmt = $conn->prepare("SELECT total_price FROM orders WHERE payment_status = 'complete'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $total_completes += $row['total_price'];
}
$stmt->close();

$num_of_orders = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc()['cnt'];
$num_of_products = $conn->query("SELECT COUNT(*) as cnt FROM products")->fetch_assoc()['cnt'];
$num_of_users = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE user_type = 'user'")->fetch_assoc()['cnt'];
$num_of_admins = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE user_type = 'admin'")->fetch_assoc()['cnt'];
$num_of_registered = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'];
$num_of_messages = $conn->query("SELECT COUNT(*) as cnt FROM message")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
  <title>Admin Panel</title>
</head>
<body>
  <?php include 'admin_header.php'; ?>

  <div class="line4"></div>
  <section class="dashboard container my-4">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3>$<?= htmlspecialchars($total_pendings) ?>/-</h3>
            <p>Total Pendings</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3>$<?= htmlspecialchars($total_completes) ?>/-</h3>
            <p>Total Completes</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3><?= htmlspecialchars($num_of_orders) ?></h3>
            <p>Orders Placed</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3><?= htmlspecialchars($num_of_products) ?></h3>
            <p>Products Added</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3><?= htmlspecialchars($num_of_users) ?></h3>
            <p>Total Normal Users</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3><?= htmlspecialchars($num_of_admins) ?></h3>
            <p>Total Admins</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3><?= htmlspecialchars($num_of_registered) ?></h3>
            <p>Total Registered Users</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h3><?= htmlspecialchars($num_of_messages) ?></h3>
            <p>New Messages</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>