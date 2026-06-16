<?php
require_once 'auth_check.php';
require_once '../config.php';

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='user'"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as s FROM orders WHERE status != 'cancelled'"))['s'] ?? 0;
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'];

// Recent orders
$recent_orders = mysqli_query($conn, "
    SELECT o.*, u.full_name
    FROM orders o JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC LIMIT 8
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Dashboard</div>
            <div style="font-size:14px; color:var(--text-muted);">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</div>
        </div>
        <div class="admin-content">

            <!-- Stat Cards -->
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-icon blue">📦</div>
                    <div>
                        <div class="stat-num"><?= $total_products ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">👥</div>
                    <div>
                        <div class="stat-num"><?= $total_users ?></div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow">🛒</div>
                    <div>
                        <div class="stat-num"><?= $total_orders ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">💰</div>
                    <div>
                        <div class="stat-num">৳<?= number_format($total_revenue) ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>

            <?php if ($pending_orders > 0): ?>
            <div class="alert alert-info" style="margin-bottom:24px;">
                📬 You have <strong><?= $pending_orders ?></strong> pending order<?= $pending_orders!=1?'s':'' ?> waiting for action.
                <a href="orders.php" style="font-weight:700; margin-left:8px;">View Orders →</a>
            </div>
            <?php endif; ?>

            <!-- Recent Orders Table -->
            <div class="data-table-card">
                <div class="data-table-header">
                    <div class="data-table-title">Recent Orders</div>
                    <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
                </div>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--primary);">#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td style="font-weight:600;">৳<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <?php
                                    $badges = ['pending'=>'badge-warning','processing'=>'badge-primary','shipped'=>'badge-primary','delivered'=>'badge-success','cancelled'=>'badge-danger'];
                                    $cls = $badges[$order['status']] ?? 'badge-primary';
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= ucfirst($order['status']) ?></span>
                                </td>
                                <td style="color:var(--text-muted); font-size:13px;"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="orders.php?update=<?= $order['id'] ?>" class="btn btn-outline btn-sm">Update</a>
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
