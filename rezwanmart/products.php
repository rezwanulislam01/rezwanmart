<?php
session_start();
require_once 'config.php';

// Filter by category
$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$search = trim($_GET['search'] ?? '');

$where = "WHERE 1=1";
if ($cat_id > 0) $where .= " AND p.category_id = $cat_id";
if ($search)     $where .= " AND p.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";

$products_result = mysqli_query($conn, "
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    $where
    ORDER BY p.created_at DESC
");

$categories_result = mysqli_query($conn, "SELECT * FROM categories");
$current_cat = $cat_id > 0 ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM categories WHERE id=$cat_id")) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?= $current_cat ? htmlspecialchars($current_cat['name']) : 'All Products' ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a> › Products<?= $current_cat ? ' › ' . htmlspecialchars($current_cat['name']) : '' ?>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div style="display:flex; gap:24px; align-items:flex-start;">

        <!-- Sidebar Filter -->
        <div style="width:220px; flex-shrink:0;">
            <div style="background:var(--bg-white); border-radius:var(--radius-lg); border:1px solid var(--border); padding:20px; position:sticky; top:84px;">
                <div style="font-weight:700; font-size:14px; margin-bottom:14px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Categories</div>
                <div style="display:flex; flex-direction:column; gap:4px;">
                    <a href="products.php"
                       style="padding:8px 12px; border-radius:var(--radius-sm); font-size:14px; font-weight:<?= $cat_id==0?'600':'400' ?>; color:<?= $cat_id==0?'var(--primary)':'var(--text-secondary)' ?>; background:<?= $cat_id==0?'var(--primary-light)':'' ?>; transition:all 0.2s;">
                        All Products
                    </a>
                    <?php
                    $categories_result2 = mysqli_query($conn, "SELECT * FROM categories");
                    while($cat = mysqli_fetch_assoc($categories_result2)):
                        $active = $cat_id == $cat['id'];
                    ?>
                    <a href="products.php?cat=<?= $cat['id'] ?>"
                       style="padding:8px 12px; border-radius:var(--radius-sm); font-size:14px; font-weight:<?= $active?'600':'400' ?>; color:<?= $active?'var(--primary)':'var(--text-secondary)' ?>; background:<?= $active?'var(--primary-light)':'' ?>; transition:all 0.2s;">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                    <?php endwhile; ?>
                </div>

                <!-- Search Filter -->
                <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border);">
                    <div style="font-weight:700; font-size:14px; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Search</div>
                    <form method="GET" action="">
                        <?php if($cat_id): ?><input type="hidden" name="cat" value="<?= $cat_id ?>"> <?php endif; ?>
                        <input type="text" name="search" class="form-control" placeholder="Search products..."
                               value="<?= htmlspecialchars($search) ?>" style="font-size:13px; padding:8px 12px;">
                        <button type="submit" class="btn btn-primary btn-sm btn-block" style="margin-top:8px;">Search</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div style="flex:1;">
            <?php $count = mysqli_num_rows($products_result); ?>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                <div style="font-size:14px; color:var(--text-muted);"><?= $count ?> product<?= $count!=1?'s':'' ?> found</div>
            </div>

            <?php if ($count === 0): ?>
                <div class="empty-state">
                    <div class="icon">🔍</div>
                    <h3>No products found</h3>
                    <p>Try a different category or search term.</p>
                    <a href="products.php" class="btn btn-primary mt-2">View All Products</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php while($product = mysqli_fetch_assoc($products_result)): ?>
                        <div class="product-card">
                            <div class="product-img-wrap">
                                <img src="/rezwanmart/uploads/<?= htmlspecialchars($product['image'] ?? 'default.jpg') ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     onerror="this.src='https://placehold.co/300x300/eff6ff/2563eb?text=No+Image'">
                                <?php if ($product['category_name']): ?>
                                    <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-body">
                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div style="font-size:13px; color:var(--text-muted); margin-bottom:8px;">
                                    Stock: <?= $product['stock'] > 0 ? '<span style="color:var(--success)">In Stock</span>' : '<span style="color:var(--danger)">Out of Stock</span>' ?>
                                </div>
                                <div class="product-price"><span class="currency">৳ </span><?= number_format($product['price'], 2) ?></div>
                                <div class="product-actions">
                                    <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">Details</a>
                                    <?php if ($product['stock'] > 0): ?>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <a href="cart_add.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">Add to Cart</a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-primary btn-sm">Add to Cart</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button class="btn btn-sm" style="background:var(--bg); color:var(--text-muted); cursor:not-allowed;" disabled>Out of Stock</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
