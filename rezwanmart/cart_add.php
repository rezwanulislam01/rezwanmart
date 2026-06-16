<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$product_id = (int)($_GET['id'] ?? 0);
$user_id    = $_SESSION['user_id'];

if ($product_id > 0) {
    // Check if already in cart
    $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['quantity'] + 1;
        mysqli_query($conn, "UPDATE cart SET quantity=$new_qty WHERE id={$row['id']}");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    }
}

header('Location: cart.php?added=1');
exit;
?>
