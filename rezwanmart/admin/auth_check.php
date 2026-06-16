<?php
// admin/auth_check.php — Include at top of every admin page
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /rezwanmart/login.php');
    exit;
}
?>
