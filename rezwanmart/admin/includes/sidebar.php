<?php
// admin/includes/sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="admin-logo">
        <span>⚡</span> Rezwan<span>Mart</span>
    </div>
    <nav class="admin-nav">
        <div class="admin-nav-label">Main</div>
        <a href="/rezwanmart/admin/dashboard.php" class="admin-nav-item <?= $current=='dashboard.php'?'active':'' ?>">
            📊 Dashboard
        </a>
        <div class="admin-nav-label">Catalog</div>
        <a href="/rezwanmart/admin/products.php" class="admin-nav-item <?= $current=='products.php'?'active':'' ?>">
            📦 Products
        </a>
        <a href="/rezwanmart/admin/add_product.php" class="admin-nav-item <?= $current=='add_product.php'?'active':'' ?>">
            ➕ Add Product
        </a>
        <a href="/rezwanmart/admin/categories.php" class="admin-nav-item <?= $current=='categories.php'?'active':'' ?>">
            🏷️ Categories
        </a>
        <div class="admin-nav-label">Orders</div>
        <a href="/rezwanmart/admin/orders.php" class="admin-nav-item <?= $current=='orders.php'?'active':'' ?>">
            🛒 Orders
        </a>
        <div class="admin-nav-label">Users</div>
        <a href="/rezwanmart/admin/users.php" class="admin-nav-item <?= $current=='users.php'?'active':'' ?>">
            👥 Users
        </a>
        <div class="admin-nav-label" style="margin-top:16px;"></div>
        <a href="/rezwanmart/index.php" class="admin-nav-item">🌐 View Site</a>
        <a href="/rezwanmart/logout.php" class="admin-nav-item" style="color:#f87171;">🚪 Logout</a>
    </nav>
</aside>
