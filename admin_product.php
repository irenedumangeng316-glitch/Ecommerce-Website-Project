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

// Add product
if (isset($_POST['add_product'])) {
    $product_name = trim($_POST['name']);
    $product_price = floatval($_POST['price']);
    $product_detail = trim($_POST['detail']);
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'image/' . basename($image);

    // Check if product name exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: admin_product.php?status=exists");
    } else {
        if ($image_size > 2000000) {
            header("Location: admin_product.php?status=large");
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, price, product_detail, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdss", $product_name, $product_price, $product_detail, $image);
            if ($stmt->execute()) {
                move_uploaded_file($image_tmp_name, $image_folder);
                header("Location: admin_product.php?status=added");
            } else {
                header("Location: admin_product.php?status=error");
            }
        }
    }
    $stmt->close();
    exit();
}

// Delete product
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Delete image
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        unlink('image/' . $row['image']);
    }
    $stmt->close();

    // Delete product and related cart/wishlist entries
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM cart WHERE pid = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE pid = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_product.php?status=deleted");
    exit();
}

// Update product
if (isset($_POST['update_product'])) {
    $update_id = intval($_POST['update_id']);
    $update_name = trim($_POST['update_name']);
    $update_price = floatval($_POST['update_price']);
    $update_detail = trim($_POST['update_detail']);
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'image/' . basename($update_image);

    if (!empty($update_image)) {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, product_detail=?, image=? WHERE id=?");
        $stmt->bind_param("sdssi", $update_name, $update_price, $update_detail, $update_image, $update_id);
        if ($stmt->execute()) {
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            header("Location: admin_product.php?status=updated");
        } else {
            header("Location: admin_product.php?status=error");
        }
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, product_detail=? WHERE id=?");
        $stmt->bind_param("sdsi", $update_name, $update_price, $update_detail, $update_id);
        if ($stmt->execute()) {
            header("Location: admin_product.php?status=updated");
        } else {
            header("Location: admin_product.php?status=error");
        }
    }
    $stmt->close();
    exit();
}
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
  <title>Admin Panel - Products</title>
</head>
<body>
  <?php include 'admin_header.php'; ?>

  <!-- Alerts -->
  <?php if (isset($_GET['status'])): ?>
    <?php
      $status = $_GET['status'];
      $alertClass = "alert-success";
      $message = "";
      switch ($status) {
        case 'exists': $message = "Product name already exists!"; $alertClass="alert-warning"; break;
        case 'large': $message = "Image size is too large!"; $alertClass="alert-warning"; break;
        case 'added': $message = "Product added successfully!"; break;
        case 'updated': $message = "Product updated successfully!"; break;
        case 'deleted': $message = "Product deleted successfully!"; break;
        case 'error': $message = "An error occurred!"; $alertClass="alert-danger"; break;
      }
    ?>
    <?php if ($message): ?>
      <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Add Product Form -->
  <section class="add-products container my-4">
    <form method="POST" enctype="multipart/form-data" class="card p-3">
      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Product Price</label>
        <input type="number" name="price" class="form-control" step="0.01" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Product Detail</label>
        <textarea name="detail" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Product Image</label>
        <input type="file" name="image" class="form-control" accept="image/*" required>
      </div>
      <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
    </form>
  </section>

  <!-- Show Products -->
  <section class="show-products container my-4">
    <div class="row">
      <?php
      $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
      if ($result && $result->num_rows > 0):
        while ($fetch_products = $result->fetch_assoc()):
      ?>
      <div class="col-md-4">
        <div class="card mb-3">
          <img src="image/<?= htmlspecialchars($fetch_products['image']) ?>" class="card-img-top">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($fetch_products['name']) ?></h5>
            <p class="card-text">$<?= htmlspecialchars($fetch_products['price']) ?></p>
            <p><?= htmlspecialchars($fetch_products['product_detail']) ?></p>
            <a href="admin_product.php?edit=<?= $fetch_products['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="admin_product.php?delete=<?= $fetch_products['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Do You Want to Delete this Product?');">Delete</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="alert alert-secondary">No Products Added Yet!</div>
    <?php endif; ?>
    </div>
  </section>

  <!-- Update Product Form -->
  <section class="update-container container my-4">
    <?php 
      if (isset($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_result = $stmt->get_result();
        if ($edit_result && $edit_result->num_rows > 0):
          $fetch_edit = $edit_result->fetch_assoc();
    ?>
    <form method="POST" enctype="multipart/form-data" class="card p-3">
      <img src="image/<?= htmlspecialchars($fetch_edit['image']) ?>" class="img-fluid mb-3">
      <input type="hidden" name="update_id" value="<?= $fetch_edit['id'] ?>">
      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="update_name" value="<?= htmlspecialchars($fetch_edit['name']) ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Product Price</label>
        <input type="number" name="update_price" min="0" value="<?= htmlspecialchars($fetch_edit['price']) ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Product Detail</label>
        <textarea name="update_detail" class="form-control"><?= htmlspecialchars($fetch_edit['product_detail']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Product Image</label>
        <input type="file" name="update_image" class="form-control" accept="image/*">
      </div>
      <button type="submit" name="update_product" class="btn btn-success">Update</button>
      <a href="admin_product.php" class="btn btn-secondary">Cancel</a>
    </form>
    <?php 
        endif;
        $stmt->close();
      }
    ?>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>