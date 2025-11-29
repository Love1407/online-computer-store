<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid item");
}

if (isset($_SESSION['user_id'])) {
    $pdo->prepare("DELETE FROM cart_items WHERE id=? AND user_id=?")
        ->execute([$id, $_SESSION['user_id']]);
} else {
    unset($_SESSION['cart'][$id]);
}

header("Location: cart.php");
exit;
?>