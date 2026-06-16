<?php
require_once 'auth_check.php';
require_once '../config.php';

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header('Location: products.php?deleted=1');
    exit;
}

$products = mysqli_query($conn, "
    SELECT p.*, c.name as category_name
    FROM products p LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Products</div>
            <a href="add_product.php" class="btn btn-primary btn-sm">+ Add Product</a>
        </div>
        <div class="admin-content">

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">✅ Product deleted successfully.</div>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">✅ Product updated successfully.</div>
            <?php endif; ?>

            <div class="data-table-card">
                <div class="data-table-header">
                    <div class="data-table-title">All Products (<?= mysqli_num_rows($products) ?>)</div>
                </div>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td>
                                    <img src="/rezwanmart/uploads/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:var(--radius);border:1px solid var(--border);"
                                         onerror="this.src='https://placehold.co/50x50/eff6ff/2563eb?text=?'">
                                </td>
                                <td style="font-weight:600; max-width:200px;"><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                                <td style="font-weight:700; color:var(--primary);">৳<?= number_format($p['price'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $p['stock'] > 0 ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $p['stock'] ?>
                                    </span>
                                </td>
                                <td style="display:flex; gap:6px;">
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
                                    <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm confirm-delete">🗑️ Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
