<?php
require_once 'auth_check.php';
require_once '../config.php';

$users = mysqli_query($conn, "
    SELECT u.*, COUNT(o.id) as order_count
    FROM users u LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users — RezwanMart Admin</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="admin-topbar-title">Users</div>
        </div>
        <div class="admin-content">
            <div class="data-table-card">
                <div class="data-table-header">
                    <div class="data-table-title">Registered Users (<?= mysqli_num_rows($users) ?>)</div>
                </div>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Orders</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td style="color:var(--text-muted);"><?= $user['id'] ?></td>
                                <td style="font-weight:600;"><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone'] ?? '—') ?></td>
                                <td>
                                    <span class="badge badge-primary"><?= $user['order_count'] ?> order<?= $user['order_count']!=1?'s':'' ?></span>
                                </td>
                                <td style="font-size:13px; color:var(--text-muted);"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
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
