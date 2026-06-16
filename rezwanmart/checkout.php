<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart
$cart_result = mysqli_query($conn, "
    SELECT c.quantity, p.id as product_id, p.name, p.price, p.stock
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$cart_items = [];
$subtotal   = 0;
while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['item_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['item_total'];
    $cart_items[] = $row;
}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$shipping = 60;
$total    = $subtotal + $shipping;

// Fetch user info
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');

    if (empty($shipping_address)) $errors[] = 'Shipping address is required.';
    if (empty($phone))            $errors[] = 'Phone number is required.';

    if (empty($errors)) {
        // Create order
        $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'ids', $user_id, $total, $shipping_address);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);

        // Insert order items
        foreach ($cart_items as $item) {
            $stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, 'iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);

            // Update stock
            mysqli_query($conn, "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}");
        }

        // Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

        $success = true;
        $_SESSION['last_order_id'] = $order_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<?php if ($success): ?>
<!-- ORDER SUCCESS -->
<div class="container">
    <div class="success-page">
        <div class="success-icon">✅</div>
        <h2 style="font-family:var(--font-display); font-size:28px; margin-bottom:8px;">Order Placed Successfully!</h2>
        <p style="color:var(--text-muted); margin-bottom:24px;">
            Thank you for shopping at RezwanMart! Your order <strong>#<?= $_SESSION['last_order_id'] ?></strong> has been placed.
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="orders.php" class="btn btn-primary">View My Orders</a>
            <a href="products.php" class="btn btn-outline">Continue Shopping</a>
        </div>
    </div>
</div>

<?php else: ?>

<div class="page-header">
    <div class="container">
        <h1>Checkout</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> › <a href="cart.php">Cart</a> › Checkout</div>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="display:grid; grid-template-columns: 1fr 360px; gap:24px; align-items:start;">

            <!-- Shipping Form -->
            <div style="background:var(--bg-white); border-radius:var(--radius-lg); border:1px solid var(--border); padding:28px;">
                <h3 style="font-family:var(--font-display); font-size:18px; font-weight:700; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid var(--border);">
                    Shipping Information
                </h3>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" readonly style="background:var(--bg);">
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number *</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                           placeholder="01XXXXXXXXX"
                           value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="shipping_address">Delivery Address *</label>
                    <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3"
                              placeholder="House/Road/Area, City" required><?= htmlspecialchars($_POST['shipping_address'] ?? $user['address'] ?? '') ?></textarea>
                </div>

                <div style="padding:16px; background:var(--primary-light); border-radius:var(--radius); border:1px solid #bfdbfe;">
                    <div style="font-weight:600; color:var(--primary); margin-bottom:4px;">💳 Payment Method</div>
                    <div style="font-size:14px; color:var(--text-secondary);">Cash on Delivery (COD)</div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="cart-summary">
                    <div class="cart-summary-title">Your Order</div>

                    <?php foreach ($cart_items as $item): ?>
                    <div style="display:flex; justify-content:space-between; padding:8px 0; font-size:14px; border-bottom:1px solid var(--border-light);">
                        <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                        <span style="font-weight:600;">৳<?= number_format($item['item_total'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>

                    <div class="summary-row" style="margin-top:8px;"><span>Subtotal</span><span>৳<?= number_format($subtotal, 2) ?></span></div>
                    <div class="summary-row"><span>Shipping</span><span>৳<?= number_format($shipping, 2) ?></span></div>
                    <div class="summary-row total"><span>Total</span><span>৳<?= number_format($total, 2) ?></span></div>

                    <button type="submit" class="btn btn-success btn-block btn-lg" style="margin-top:20px;">
                        ✅ Place Order
                    </button>
                    <div style="text-align:center; margin-top:10px; font-size:12px; color:var(--text-muted);">
                        🔒 Cash on Delivery — Pay when you receive
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
