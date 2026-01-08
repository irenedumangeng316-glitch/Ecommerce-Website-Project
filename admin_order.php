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

// Delete order securely
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header('Location: admin_order.php?status=deleted');
    } else {
        header('Location: admin_order.php?status=error');
    }
    $stmt->close();
    exit();
}

// Update payment status securely
if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $update_payment = $_POST['update_payment'];

    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $update_payment, $order_id);
    if ($stmt->execute()) {
        header('Location: admin_order.php?status=updated');
    } else {
        header('Location: admin_order.php?status=error');
    }
    $stmt->close();
    exit();
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search setup
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$query = "SELECT * FROM orders WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR payment_status LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = "sss";
}

$query .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Count total orders for pagination
$countQuery = "SELECT COUNT(*) as total FROM orders WHERE 1";
if (!empty($search)) {
    $countQuery .= " AND (name LIKE ? OR email LIKE ? OR payment_status LIKE ?)";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = $countResult->fetch_assoc()['total'];
    $countStmt->close();
} else {
    $countResult = $conn->query($countQuery);
    $total = $countResult->fetch_assoc()['total'];
}

$totalPages = ceil($total / $limit);
$stmt->close();
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
    <title>Admin Panel - Orders</title>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <!-- Bootstrap Alerts -->
    <?php if (isset($_GET['status'])): ?>
        <?php
        $status = $_GET['status'];
        $alertClass = "alert-success";
        $message = "";

        switch ($status) {
            case 'deleted': $message = "Order successfully deleted!"; break;
            case 'updated': $message = "Payment status updated!"; break;
            case 'error':   $message = "An error occurred!"; $alertClass = "alert-danger"; break;
        }
        ?>
        <?php if ($message): ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <section class="order-container">
        <h1 class="title">User Orders</h1>

        <!-- Search Form -->
        <form method="get" action="admin_order.php" class="d-flex mb-3">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by name, email, or status..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="box-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($fetch_orders = $result->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><strong>User Name:</strong> <?= htmlspecialchars($fetch_orders['name']) ?></p>
                            <p><strong>User ID:</strong> <?= htmlspecialchars($fetch_orders['user_id']) ?></p>
                            <p><strong>Placed On:</strong> <?= htmlspecialchars($fetch_orders['placed_on']) ?></p>
                            <p><strong>Number:</strong> <?= htmlspecialchars($fetch_orders['number']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($fetch_orders['email']) ?></p>
                            <p><strong>Total Price:</strong> <?= htmlspecialchars($fetch_orders['total_price']) ?></p>
                            <p><strong>Method:</strong> <?= htmlspecialchars($fetch_orders['method']) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($fetch_orders['address']) ?></p>
                            <p><strong>Total Products:</strong> <?= htmlspecialchars($fetch_orders['total_products']) ?></p>
                            <p><strong>Payment Status:</strong> <?= htmlspecialchars($fetch_orders['payment_status']) ?></p>

                            <form method="post" class="d-flex align-items-center mt-2">
                                <input type="hidden" name="order_id" value="<?= $fetch_orders['id'] ?>">
                                <select name="update_payment" class="form-select me-2">
                                    <option disabled selected><?= htmlspecialchars($fetch_orders['payment_status']) ?></option>
                                    <option value="pending">Pending</option>
                                    <option value="complete">Complete</option>
                                </select>
                                <button type="submit" name="update_order" class="btn btn-primary btn-sm">Update Payment</button>
                                <a href="admin_order.php?delete=<?= $fetch_orders['id'] ?>" 
                                   class="btn btn-danger btn-sm ms-2"
                                   onclick="return confirm('Delete this order?');">Delete</a>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-secondary">No Orders Found!</div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next &raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>