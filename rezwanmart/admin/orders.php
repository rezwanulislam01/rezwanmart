<?php
require_once 'auth_check.php';
require_once '../config.php';

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    $allowed  = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
    }
    header('Location: orders.php?updated=1');
    exit;
}

$orders = mysqli_query($conn, "
    SELECT o.*, u.full_name, u.email, COUNT(oi.id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Orders</div>
        </div>
        <div class="admin-content">

            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">✅ Order status updated.</div>
            <?php endif; ?>

            <div class="data-table-card">
                <div class="data-table-header">
                    <div class="data-table-title">All Orders (<?= mysqli_num_rows($orders) ?>)</div>
                </div>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td style="font-weight:700; color:var(--primary);">#<?= $order['id'] ?></td>
                                <td>
                                    <div style="font-weight:600; font-size:14px;"><?= htmlspecialchars($order['full_name']) ?></div>
                                    <div style="font-size:12px; color:var(--text-muted);"><?= htmlspecialchars($order['email']) ?></div>
                                </td>
                                <td><?= $order['item_count'] ?></td>
                                <td style="font-weight:700;">৳<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <?php
                                    $badges = ['pending'=>'badge-warning','processing'=>'badge-primary','shipped'=>'badge-primary','delivered'=>'badge-success','cancelled'=>'badge-danger'];
                                    $cls = $badges[$order['status']] ?? 'badge-primary';
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= ucfirst($order['status']) ?></span>
                                </td>
                                <td style="font-size:13px; color:var(--text-muted);"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <select name="status" class="form-control" style="padding:6px 10px; font-size:13px; width:140px;">
                                            <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                                <option value="<?= $s ?>" <?= $order['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </form>
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
