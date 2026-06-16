<?php
require_once 'auth_check.php';
require_once '../config.php';

$errors  = [];
$success = '';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (empty($name))    $errors[] = 'Product name is required.';
    if ($price <= 0)     $errors[] = 'Price must be greater than 0.';
    if ($stock < 0)      $errors[] = 'Stock cannot be negative.';

    // Image upload
    $image_name = 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $errors[] = 'Only JPG, PNG, GIF, WEBP images are allowed.';
        } elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
            $errors[] = 'Image size must be under 3MB.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('product_') . '.' . $ext;
            $upload_path = dirname(__DIR__) . '/uploads/' . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $errors[] = 'Failed to upload image. Check uploads/ folder permissions.';
                $image_name = 'default.jpg';
            }
        }
    }

    if (empty($errors)) {
        $cat = $category_id > 0 ? $category_id : 'NULL';
        $stmt = mysqli_prepare($conn, "INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'issdis', $category_id, $name, $description, $price, $stock, $image_name);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Product added successfully!';
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Add New Product</div>
            <a href="products.php" class="btn btn-outline btn-sm">← Back to Products</a>
        </div>
        <div class="admin-content">

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= $success ?> <a href="products.php">View All Products</a> | <a href="add_product.php">Add Another</a></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            <?php endif; ?>

            <div style="max-width:680px;">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div style="background:var(--bg-white); border-radius:var(--radius-lg); border:1px solid var(--border); padding:28px;">

                        <div class="form-group">
                            <label class="form-label" for="name">Product Name *</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   placeholder="e.g. Wireless Earbuds Pro"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="form-group">
                                <label class="form-label" for="price">Price (৳) *</label>
                                <input type="number" id="price" name="price" class="form-control"
                                       placeholder="0.00" step="0.01" min="0"
                                       value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="stock">Stock Quantity *</label>
                                <input type="number" id="stock" name="stock" class="form-control"
                                       placeholder="0" min="0"
                                       value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control">
                                <option value="">— Select Category —</option>
                                <?php
                                $categories2 = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
                                while ($cat = mysqli_fetch_assoc($categories2)): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3"
                                      placeholder="Product description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="productImage">Product Image</label>
                            <input type="file" id="productImage" name="image" class="form-control"
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                            <div class="text-muted mt-1">Max 3MB. JPG, PNG, GIF, WEBP accepted.</div>
                            <img id="imagePreview" src="" alt="Preview"
                                 style="display:none; margin-top:12px; max-height:160px; border-radius:var(--radius); border:1px solid var(--border);">
                        </div>

                        <div style="display:flex; gap:10px; margin-top:8px;">
                            <button type="submit" class="btn btn-primary btn-lg">➕ Add Product</button>
                            <a href="products.php" class="btn btn-outline btn-lg">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
