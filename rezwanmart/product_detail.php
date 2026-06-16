<?php
session_start();
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: products.php'); exit; }

$product = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, c.name as category_name
    FROM products p LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = $id
"));

if (!$product) { header('Location: products.php'); exit; }

// Related products
$related = mysqli_query($conn, "
    SELECT * FROM products
    WHERE category_id = {$product['category_id']} AND id != $id
    LIMIT 4
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a> ›
            <a href="products.php">Products</a> ›
            <?php if ($product['category_name']): ?>
                <a href="products.php?cat=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> ›
            <?php endif; ?>
            <?= htmlspecialchars($product['name']) ?>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:start;">

        <!-- Product Image -->
        <div style="background:var(--bg-white); border-radius:var(--radius-xl); border:1px solid var(--border); overflow:hidden;">
            <img src="/rezwanmart/uploads/<?= htmlspecialchars($product['image'] ?? 'default.jpg') ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 style="width:100%; aspect-ratio:1; object-fit:cover;"
                 onerror="this.src='https://placehold.co/600x600/eff6ff/2563eb?text=No+Image'">
        </div>

        <!-- Product Info -->
        <div>
            <?php if ($product['category_name']): ?>
                <span class="badge badge-primary" style="margin-bottom:12px;"><?= htmlspecialchars($product['category_name']) ?></span>
            <?php endif; ?>

            <h1 style="font-family:var(--font-display); font-size:28px; font-weight:700; margin-bottom:12px; line-height:1.3;">
                <?= htmlspecialchars($product['name']) ?>
            </h1>

            <div style="font-size:32px; font-weight:800; color:var(--primary); font-family:var(--font-display); margin-bottom:16px;">
                ৳<?= number_format($product['price'], 2) ?>
            </div>

            <!-- Stock -->
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px;">
                <?php if ($product['stock'] > 0): ?>
                    <span style="width:10px;height:10px;background:var(--success);border-radius:50%;display:inline-block;"></span>
                    <span style="color:var(--success); font-weight:600; font-size:14px;">In Stock (<?= $product['stock'] ?> available)</span>
                <?php else: ?>
                    <span style="width:10px;height:10px;background:var(--danger);border-radius:50%;display:inline-block;"></span>
                    <span style="color:var(--danger); font-weight:600; font-size:14px;">Out of Stock</span>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <?php if ($product['description']): ?>
            <div style="background:var(--bg); border-radius:var(--radius); padding:16px; margin-bottom:24px; font-size:15px; color:var(--text-secondary); line-height:1.7;">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <?php if ($product['stock'] > 0): ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="cart_add.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-lg">🛒 Add to Cart</a>
                        <a href="cart_add.php?id=<?= $product['id'] ?>&redirect=checkout" class="btn btn-success btn-lg">⚡ Buy Now</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-lg">🛒 Add to Cart</a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-lg" style="background:var(--bg); color:var(--text-muted); cursor:not-allowed;" disabled>Out of Stock</button>
                <?php endif; ?>
                <a href="products.php" class="btn btn-outline btn-lg">← Back</a>
            </div>

            <!-- Features -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:28px; padding-top:24px; border-top:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary);">
                    <span style="font-size:18px;">🚚</span> Fast Delivery
                </div>
                <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary);">
                    <span style="font-size:18px;">↩️</span> Easy Returns
                </div>
                <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary);">
                    <span style="font-size:18px;">🔒</span> Secure Checkout
                </div>
                <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary);">
                    <span style="font-size:18px;">💳</span> Cash on Delivery
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (mysqli_num_rows($related) > 0): ?>
    <div style="margin-top:60px;">
        <div class="section-title">Related Products</div>
        <div class="section-sub">You might also like these</div>
        <div class="products-grid">
            <?php while($rel = mysqli_fetch_assoc($related)): ?>
            <div class="product-card">
                <div class="product-img-wrap">
                    <img src="/rezwanmart/uploads/<?= htmlspecialchars($rel['image'] ?? 'default.jpg') ?>"
                         alt="<?= htmlspecialchars($rel['name']) ?>"
                         onerror="this.src='https://placehold.co/300x300/eff6ff/2563eb?text=No+Image'">
                </div>
                <div class="product-body">
                    <div class="product-name"><?= htmlspecialchars($rel['name']) ?></div>
                    <div class="product-price"><span class="currency">৳ </span><?= number_format($rel['price'], 2) ?></div>
                    <div class="product-actions">
                        <a href="product_detail.php?id=<?= $rel['id'] ?>" class="btn btn-outline btn-sm">Details</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="cart_add.php?id=<?= $rel['id'] ?>" class="btn btn-primary btn-sm">Add to Cart</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary btn-sm">Add to Cart</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
