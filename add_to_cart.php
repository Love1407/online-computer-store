<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id < 1) {
    header("Location: exploreproducts.php");
    exit;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE id=?");
        $stmt->execute([$quantity, $existing['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?,?,?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
} else {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'quantity'   => $quantity
        ];
    }
}

header("Location: exploreproducts.php");
exit;