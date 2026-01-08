<?php
// admin_user.php
// Secure Admin User Management with Activity Log + Export

@include 'config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

// Helper: Log activity
function logActivity($conn, $admin_id, $action, $details) {
    $stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action, details, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $admin_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $role, $password);
    $stmt->execute();
    $stmt->close();

    logActivity($conn, $_SESSION['admin_id'], "Add User", "Added user: $name ($email) as $role");
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    $stmt->close();

    logActivity($conn, $_SESSION['admin_id'], "Update User", "Updated user ID $id: $name ($email) role=$role");
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    logActivity($conn, $_SESSION['admin_id'], "Delete User", "Deleted user ID $id");

    header('location:admin_user.php');
    exit();
}

// Handle Export Logs
if (isset($_POST['export_logs'])) {
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $query = "SELECT a.id, u.name AS admin_name, a.action, a.details, a.timestamp 
              FROM activity_log a 
              LEFT JOIN users u ON a.admin_id = u.id 
              WHERE DATE(a.timestamp) BETWEEN ? AND ? 
              ORDER BY a.timestamp DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=activity_log.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Admin', 'Action', 'Details', 'Timestamp']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Fetch Users
$result = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");

// Fetch Activity Log (filter if applied)
$logQuery = "SELECT a.id, u.name AS admin_name, a.action, a.details, a.timestamp 
             FROM activity_log a 
             LEFT JOIN users u ON a.admin_id = u.id ";

if (isset($_POST['filter_logs'])) {
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $logQuery .= "WHERE DATE(a.timestamp) BETWEEN '$start' AND '$end' ";
}

$logQuery .= "ORDER BY a.timestamp DESC LIMIT 50";
$logResult = $conn->query($logQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Manage Admin Users</h2>

    <!-- Add User Form -->
    <div class="card mb-4">
        <div class="card-header">Add New User</div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="moderator">Moderator</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </form>
        </div>
    </div>

    <!-- User Table -->
    <div class="card mb-4">
        <div class="card-header">Existing Users</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']); ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['role']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">Edit</button>
                            <a href="admin_user.php?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-select" required>
                                            <option value="admin" <?= $row['role']=='admin'?'selected':''; ?>>Admin</option>
                                            <option value="moderator" <?= $row['role']=='moderator'?'selected':''; ?>>Moderator</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update_user" class="btn btn-success">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

        <!-- Activity Log -->
    <div class="card mb-4">
        <div class="card-header">Activity Log</div>
        <div class="card-body">

            <!-- Filter Form -->
            <form method="post" class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" name="filter_logs" class="btn btn-primary me-2">Filter</button>
                    <button type="submit" name="export_logs" class="btn btn-success">Export CSV</button>
                </div>
            </form>

            <!-- Log Table -->
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($log = $logResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['id']); ?></td>
                        <td><?= htmlspecialchars($log['admin_name']); ?></td>
                        <td><?= htmlspecialchars($log['action']); ?></td>
                        <td><?= htmlspecialchars($log['details']); ?></td>
                        <td><?= htmlspecialchars($log['timestamp']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>