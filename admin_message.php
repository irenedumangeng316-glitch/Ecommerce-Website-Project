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

// Delete message securely
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM message WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header('Location: admin_message.php?status=deleted');
    } else {
        header('Location: admin_message.php?status=error');
    }
    $stmt->close();
    exit();
}

// Mark message as read
if (isset($_GET['read'])) {
    $read_id = intval($_GET['read']);
    $stmt = $conn->prepare("UPDATE message SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $read_id);
    if ($stmt->execute()) {
        header('Location: admin_message.php?status=read');
    } else {
        header('Location: admin_message.php?status=error');
    }
    $stmt->close();
    exit();
}

// Count unread messages for notification badge
$newMessageQuery = $conn->query("SELECT COUNT(*) as cnt FROM message WHERE status='unread'");
$newCount = $newMessageQuery->fetch_assoc()['cnt'];

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Sorting setup
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
switch ($sort) {
    case 'oldest': $orderBy = "id ASC"; break;
    case 'name':   $orderBy = "name ASC"; break;
    case 'email':  $orderBy = "email ASC"; break;
    default:       $orderBy = "id DESC"; // newest
}

// Search setup
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$query = "SELECT * FROM message WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = "sss";
}

$query .= " ORDER BY $orderBy LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Count total messages for pagination
$countQuery = "SELECT COUNT(*) as total FROM message WHERE 1";
if (!empty($search)) {
    $countQuery .= " AND (name LIKE ? OR email LIKE ? OR message LIKE ?)";
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
    <title>Admin Panel</title>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <!-- Notification Bar -->
    <div class="bg-dark text-white p-2 mb-3">
        <?php if ($newCount > 0): ?>
            <span class="badge bg-warning text-dark">🔔 <?= $newCount ?> New Message(s)</span>
        <?php else: ?>
            <span>No new messages</span>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Alerts -->
    <?php if (isset($_GET['status'])): ?>
        <?php
        $status = $_GET['status'];
        $alertClass = "alert-success";
        $message = "";

        switch ($status) {
            case 'deleted': $message = "Message successfully deleted!"; break;
            case 'sent':    $message = "Message successfully sent!"; break;
            case 'read':    $message = "Message marked as read!"; break;
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

    <section class="message-container">
        <h1 class="title">Messages</h1>

        <!-- Search + Sort Form -->
        <form method="get" action="admin_message.php" class="d-flex mb-3">
            <input type="text" name="search" class="form-control me-2" placeholder="Search messages..." value="<?= htmlspecialchars($search) ?>">
            <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest</option>
                <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest</option>
                <option value="name" <?= $sort=='name'?'selected':'' ?>>Name</option>
                <option value="email" <?= $sort=='email'?'selected':'' ?>>Email</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="box-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($fetch_message = $result->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><strong>User ID:</strong> <?= htmlspecialchars($fetch_message['id']) ?></p>
                            <p><strong>Name:</strong> <?= htmlspecialchars($fetch_message['name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($fetch_message['email']) ?></p>
                            <p><?= htmlspecialchars($fetch_message['message']) ?></p>
                            <p><strong>Status:</strong> <?= htmlspecialchars($fetch_message['status']) ?></p>

                            <!-- Action buttons -->
                            <a href="admin_message.php?delete=<?= $fetch_message['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this message?');">Delete</a>

                            <?php if ($fetch_message['status'] !== 'read' && $fetch_message['status'] !== 'replied'): ?>
                                <a href="admin_message.php?read=<?= $fetch_message['id'] ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Mark this message as read?');">Mark as Read</a>
                            <?php endif; ?>

                            <?php if ($fetch_message['status'] === 'replied'): ?>
                                <span class="badge bg-info">✔ Replied</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-secondary">No Messages Found!</div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">&laquo; Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                       