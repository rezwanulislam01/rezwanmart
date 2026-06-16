<?php
require_once 'auth_check.php';
require_once '../config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: products.php'); exit; }

$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
if (!$product) { header('Location: products.php'); exit; }

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (empty($name)) $errors[] = 'Product name is required.';
    if ($price <= 0)  $errors[] = 'Price must be greater than 0.';

    $image_name = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array(mime_content_type($_FILES['image']['tmp_name']), $allowed)) {
            $errors[] = 'Invalid image type.';
        } elseif ($_FILES['image']['size'] > 3 * 1024 * 1024) {
            $errors[] = 'Image must be under 3MB.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('product_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], dirname(__DIR__) . '/uploads/' . $new_name)) {
                $image_name = $new_name;
            }
        }
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'issdiis', $category_id, $name, $description, $price, $stock, $image_name, $id);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: products.php?updated=1');
            exit;
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
    <title>Edit Product — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Edit Product</div>
            <a href="products.php" class="btn btn-outline btn-sm">← Back</a>
        </div>
        <div class="admin-content">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            <?php endif; ?>

            <div style="max-width:680px;">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div style="background:var(--bg-white); border-radius:var(--radius-lg); border:1px solid var(--border); padding:28px;">

                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($_POST['name'] ?? $product['name']) ?>" required>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="form-group">
                                <label class="form-label">Price (৳) *</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0"
                                       value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stock *</label>
                                <input type="number" name="stock" class="form-control" min="0"
                                       value="<?= htmlspecialchars($_POST['stock'] ?? $product['stock']) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">— Select Category —</option>
                                <?php
                                $cats = mysqli_query($conn, "SELECT * FROM categories");
                                while ($c = mysqli_fetch_assoc($cats)):
                                    $sel = ($product['category_id'] == $c['id']) ? 'selected' : '';
                                ?>
                                <option value="<?= $c['id'] ?>" <?= $sel ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? $product['description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Product Image</label>
                            <?php if ($product['image'] && $product['image'] !== 'default.jpg'): ?>
                                <div style="margin-bottom:10px;">
                                    <img src="/rezwanmart/uploads/<?= htmlspecialchars($product['image']) ?>"
                                         style="height:80px; border-radius:var(--radius); border:1px solid var(--border);"
                                         onerror="this.style.display='none'">
                                    <div class="text-muted mt-1">Current image</div>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="productImage" name="image" class="form-control" accept="image/*">
                            <div class="text-muted mt-1">Leave empty to keep current image.</div>
                            <img id="imagePreview" src="" style="display:none; margin-top:10px; max-height:120px; border-radius:var(--radius);">
                        </div>

                        <div style="display:flex; gap:10px;">
                            <button type="submit" class="btn btn-primary btn-lg">💾 Save Changes</button>
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
