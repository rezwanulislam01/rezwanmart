<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $cart_id => $qty) {
        $cart_id = (int)$cart_id;
        $qty     = max(1, (int)$qty);
        mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$user_id");
    }
    header('Location: cart.php?updated=1');
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
    header('Location: cart.php?removed=1');
    exit;
}

// Fetch cart items
$cart_result = mysqli_query($conn, "
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$cart_items = [];
$subtotal   = 0;
while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['item_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['item_total'];
    $cart_items[] = $row;
}

$shipping = count($cart_items) > 0 ? 60 : 0;
$total    = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1>Shopping Cart</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> › Cart</div>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">✅ Product added to cart!</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-info">🔄 Cart updated.</div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="empty-state">
            <div class="icon">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a href="products.php" class="btn btn-primary mt-2">Browse Products</a>
        </div>
    <?php else: ?>

        <div style="display:grid; grid-template-columns: 1fr 320px; gap:24px; align-items:start;">

            <!-- Cart Items -->
            <form method="POST" action="">
                <div class="cart-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <img src="/rezwanmart/uploads/<?= htmlspecialchars($item['image']) ?>"
                                             class="cart-product-img"
                                             onerror="this.src='https://placehold.co/60x60/eff6ff/2563eb?text=?'">
                                        <div>
                                            <div style="font-weight:600; font-size:14px;"><?= htmlspecialchars($item['name']) ?></div>
                                            <div style="font-size:12px; color:var(--text-muted);">In Stock</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight:600;">৳<?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" data-action="decrease">−</button>
                                        <input type="number" class="qty-input" name="quantities[<?= $item['cart_id'] ?>]"
                                               value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                                        <button type="button" class="qty-btn" data-action="increase">+</button>
                                    </div>
                                </td>
                                <td style="font-weight:700; color:var(--primary);">৳<?= number_format($item['item_total'], 2) ?></td>
                                <td>
                                    <a href="cart.php?remove=<?= $item['cart_id'] ?>"
                                       class="btn btn-danger btn-sm confirm-delete">Remove</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:12px; display:flex; gap:10px;">
                    <button type="submit" name="update_cart" class="btn btn-outline">🔄 Update Cart</button>
                    <a href="products.php" class="btn btn-outline">← Continue Shopping</a>
                </div>
            </form>

            <!-- Order Summary -->
            <div class="cart-summary">
                <div class="cart-summary-title">Order Summary</div>
                <div class="summary-row"><span>Subtotal</span><span>৳<?= number_format($subtotal, 2) ?></span></div>
                <div class="summary-row"><span>Shipping</span><span>৳<?= number_format($shipping, 2) ?></span></div>
                <div class="summary-row total"><span>Total</span><span>৳<?= number_format($total, 2) ?></span></div>
                <a href="checkout.php" class="btn btn-primary btn-block btn-lg" style="margin-top:20px;">
                    Proceed to Checkout →
                </a>
                <div style="text-align:center; margin-top:12px; font-size:12px; color:var(--text-muted);">
                    🔒 Secure & Safe Checkout
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
