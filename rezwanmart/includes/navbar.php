<?php
// includes/navbar.php
// Cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $r = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
    $cart_count = mysqli_fetch_assoc($r)['total'] ?? 0;
}
?>
<nav class="navbar">
    <div class="container navbar-inner">
        <a href="/rezwanmart/index.php" class="navbar-brand">Rezwan<span>Mart</span></a>

        <div class="navbar-search">
            <span class="search-icon">🔍</span>
            <input type="text" id="productSearch" placeholder="Search products...">
        </div>

        <div class="navbar-links">
            <a href="/rezwanmart/index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
            <a href="/rezwanmart/products.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">Products</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/rezwanmart/cart.php" class="nav-cart">
                    🛒 Cart
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
                <a href="/rezwanmart/orders.php" class="nav-link">My Orders</a>
                <a href="/rezwanmart/logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php else: ?>
                <a href="/rezwanmart/login.php" class="nav-link">Login</a>
                <a href="/rezwanmart/register.php" class="btn btn-primary btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
