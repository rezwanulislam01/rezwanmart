<?php
require_once 'auth_check.php';
require_once '../config.php';

$errors  = [];
$success = '';

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['cat_name'] ?? '');
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
        mysqli_stmt_bind_param($stmt, 's', $name);
        if (mysqli_stmt_execute($stmt)) $success = 'Category added!';
        mysqli_stmt_close($stmt);
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header('Location: categories.php?deleted=1');
    exit;
}

$categories = mysqli_query($conn, "
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id ORDER BY c.name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Categories</div>
        </div>
        <div class="admin-content">

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= $success ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">✅ Category deleted.</div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

                <!-- Add Category Form -->
                <div style="background:var(--bg-white); border-radius:var(--radius-lg); border:1px solid var(--border); padding:24px;">
                    <h3 style="font-family:var(--font-display); font-size:17px; font-weight:700; margin-bottom:16px;">Add New Category</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Category Name *</label>
                            <input type="text" name="cat_name" class="form-control" placeholder="e.g. Mobile Phones" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-primary">➕ Add Category</button>
                    </form>
                </div>

                <!-- Categories List -->
                <div class="data-table-card">
                    <div class="data-table-header">
                        <div class="data-table-title">All Categories</div>
                    </div>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Products</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                <tr>
                                    <td style="font-weight:600;"><?= htmlspecialchars($cat['name']) ?></td>
                                    <td><span class="badge badge-primary"><?= $cat['product_count'] ?></span></td>
                                    <td>
                                        <a href="categories.php?delete=<?= $cat['id'] ?>"
                                           class="btn btn-danger btn-sm confirm-delete">🗑️ Delete</a>
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
</div>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
