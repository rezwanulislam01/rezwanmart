<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="auth-page">
    <div style="width:100%; max-width:440px;">

        <div class="auth-logo">
            <a href="index.php">Rezwan<span>Mart</span></a>
        </div>

        <div class="form-card">
            <h2 class="auth-title">Welcome Back!</h2>
            <p class="auth-sub">Login to your RezwanMart account</p>

            <?php if ($error): ?>
                <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">✅ Account created! Please login.</div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="">

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="you@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div style="position:relative;">
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="Your password" required>
                        <span class="toggle-password" data-target="password"
                              style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:16px;">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">Login</button>
            </form>

            <div class="auth-footer mt-2">
                Don't have an account? <a href="register.php">Register here</a>
            </div>

            <div style="margin-top:20px; padding:14px; background:var(--bg); border-radius:var(--radius); font-size:13px; color:var(--text-muted); text-align:center;">
                <strong>Admin Demo:</strong> admin@rezwanmart.com / password: <code>password</code>
            </div>
        </div>
    </div>
</div>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
