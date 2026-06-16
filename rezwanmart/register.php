<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Server-side validation
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');

    if (strlen($full_name) < 3)
        $errors[] = 'Full name must be at least 3 characters.';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Please enter a valid email address.';

    if (strlen($password) < 6)
        $errors[] = 'Password must be at least 6 characters.';

    if ($password !== $confirm)
        $errors[] = 'Passwords do not match.';

    // Check duplicate email
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0)
        $errors[] = 'This email is already registered.';
    mysqli_stmt_close($stmt);

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $full_name, $email, $hashed, $phone, $address);

        if (mysqli_stmt_execute($stmt)) {
            $success = 'Account created successfully! You can now login.';
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — RezwanMart</title>
    <link rel="stylesheet" href="/rezwanmart/css/style.css">
</head>
<body>
<div class="auth-page">
    <div style="width:100%; max-width:480px;">

        <div class="auth-logo">
            <a href="index.php">Rezwan<span>Mart</span></a>
        </div>

        <div class="form-card">
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-sub">Join RezwanMart and start shopping today</p>

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= $success ?> <a href="login.php">Login now →</a></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <div>⚠ <?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="">

                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control"
                           placeholder="Your full name"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="you@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                           placeholder="01XXXXXXXXX"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <div style="position:relative;">
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="Minimum 6 characters" required>
                        <span class="toggle-password" data-target="password"
                              style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:16px;">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password *</label>
                    <div style="position:relative;">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                               placeholder="Re-enter your password" required>
                        <span class="toggle-password" data-target="confirm_password"
                              style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:16px;">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Delivery Address</label>
                    <textarea id="address" name="address" class="form-control" rows="2"
                              placeholder="Your delivery address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</div>
<script src="/rezwanmart/js/script.js"></script>
</body>
</html>
