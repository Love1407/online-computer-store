<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = (int)$stmt->fetchColumn();
} else {
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $c) {
            $qty = isset($c['quantity']) ? (int)$c['quantity'] : 1;
            $cart_count += $qty;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Hmart</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.cart-count {
    background:#ff4444;
    color:white;
    padding:2px 6px;
    border-radius:12px;
    font-size:12px;
    margin-left:4px;
}
</style>
</head>
<body>

<header class="header">
    <div class="logo"><a href="/online-computer-store/index.php">Hmart</a></div>

    <div class="icons">
        <a href="/online-computer-store/cart.php" class="cart-icon">
            🛒 <span class="cart-count"><?= $cart_count ?></span>
        </a>

        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="/online-computer-store/order_history.php">My Account</a></li>
            <li><a href="/online-computer-store/logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="/online-computer-store/login.php">Login</a></li>
            <li><a href="/online-computer-store/signup.php">Sign Up</a></li>
        <?php endif; ?>
    </div>
</header>

<nav class="nav-bar">
    <ul>
        <li><a href="/online-computer-store/index.php">Home</a></li>
        <li><a href="/online-computer-store/index.php">About</a></li>
        <li><a href="/online-computer-store/exploreproducts.php">Explore Products</a></li>  
    </ul>
</nav>

<main class="container">