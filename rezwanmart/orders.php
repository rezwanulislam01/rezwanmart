<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$orders_result = mysqli_query($conn, "
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = $user_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1>My Orders</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> › My Orders</div>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <?php if (mysqli_num_rows($orders_result) === 0): ?>
        <div class="empty-state">
            <div class="icon">📦</div>
            <h3>No orders yet</h3>
            <p>You haven't placed any orders yet.</p>
            <a href="products.php" class="btn btn-primary mt-2">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="data-table-card">
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <tr>
                            <td style="font-weight:700; color:var(--primary);">#<?= $order['id'] ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['item_count'] ?> item<?= $order['item_count'] != 1 ? 's' : '' ?></td>
                            <td style="font-weight:700;">৳<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <?php
                                $badges = [
                                    'pending'    => 'badge-warning',
                                    'processing' => 'badge-primary',
                                    'shipped'    => 'badge-info',
                                    'delivered'  => 'badge-success',
                                    'cancelled'  => 'badge-danger',
                                ];
                                $cls = $badges[$order['status']] ?? 'badge-primary';
                                ?>
                                <span class="badge <?= $cls ?>"><?= ucfirst($order['status']) ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
