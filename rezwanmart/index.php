<?php
session_start();
require_once 'config.php';

// Fetch featured products (latest 8)
$products_result = mysqli_query($conn, "
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
    LIMIT 8
");

// Fetch categories
$categories_result = mysqli_query($conn, "SELECT * FROM categories");

// Count stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='user'"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RezwanMart — Shop Smart, Live Better</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-label">🛒 Bangladesh's Favorite Online Shop</div>
            <h1>Shop Smart,<br>Live <span>Better</span></h1>
            <p>Discover thousands of products at unbeatable prices. Electronics, clothing, books, and more — delivered to your door.</p>
            <div class="hero-btns">
                <a href="products.php" class="btn-hero-primary">Shop Now →</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn-hero-outline">Create Account</a>
                <?php endif; ?>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num"><?= number_format($total_products) ?>+</div>
                    <div class="hero-stat-label">Products</div>
                </div>
                <div>
                    <div class="hero-stat-num"><?= number_format($total_users) ?>+</div>
                    <div class="hero-stat-label">Happy Customers</div>
                </div>
                <div>
                    <div class="hero-stat-num"><?= number_format($total_orders) ?>+</div>
                    <div class="hero-stat-label">Orders Placed</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="section" style="padding-bottom:20px;">
    <div class="container">
        <div class="section-title">Browse Categories</div>
        <div class="section-sub">Find what you're looking for</div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="products.php" class="btn btn-primary btn-sm">All</a>
            <?php while($cat = mysqli_fetch_assoc($categories_result)): ?>
                <a href="products.php?cat=<?= $cat['id'] ?>" class="btn btn-outline btn-sm"><?= htmlspecialchars($cat['name']) ?></a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="section">
    <div class="container">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
            <div class="section-title">Featured Products</div>
            <a href="products.php" class="btn btn-outline btn-sm">View All →</a>
        </div>
        <div class="section-sub">Handpicked just for you</div>

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
                        <div class="product-price">
                            <span class="currency">৳ </span><?= number_format($product['price'], 2) ?>
                        </div>
                        <div class="product-actions">
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">Details</a>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="cart_add.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">Add to Cart</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-sm">Add to Cart</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- FEATURES STRIP -->
<section style="background: var(--bg-white); border-top:1px solid var(--border); border-bottom:1px solid var(--border); padding: 32px 0;">
    <div class="container">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:24px; text-align:center;">
            <div>
                <div style="font-size:28px; margin-bottom:8px;">🚚</div>
                <div style="font-weight:700; font-size:15px; margin-bottom:4px;">Fast Delivery</div>
                <div style="font-size:13px; color:var(--text-muted);">Delivered to your doorstep</div>
            </div>
            <div>
                <div style="font-size:28px; margin-bottom:8px;">🔒</div>
                <div style="font-weight:700; font-size:15px; margin-bottom:4px;">Secure Payment</div>
                <div style="font-size:13px; color:var(--text-muted);">Safe & encrypted transactions</div>
            </div>
            <div>
                <div style="font-size:28px; margin-bottom:8px;">↩️</div>
                <div style="font-weight:700; font-size:15px; margin-bottom:4px;">Easy Returns</div>
                <div style="font-size:13px; color:var(--text-muted);">7-day return policy</div>
            </div>
            <div>
                <div style="font-size:28px; margin-bottom:8px;">💬</div>
                <div style="font-weight:700; font-size:15px; margin-bottom:4px;">24/7 Support</div>
                <div style="font-size:13px; color:var(--text-muted);">We're always here to help</div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
