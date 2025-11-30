<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
    header('Location: login.php?error=' . urlencode('Invalid credentials'));
    exit;
}

$stmt = $pdo->prepare('SELECT id, name, email, password, is_admin FROM users WHERE email=? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password'])) {
    header('Location: login.php?error=' . urlencode('Invalid email or password'));
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['is_admin']  = $user['is_admin'];
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (!isset($item['product_id']) || !isset($item['quantity'])) continue;

        $product_id = $item['product_id'];
        $quantity   = $item['quantity'];

        $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id=? AND product_id=?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $exist = $stmt->fetch();

        if ($exist) {
            $newQty = $exist['quantity'] + $quantity;
            $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?")->execute([$newQty, $exist['id']]);
        } else {
            $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?,?,?)")
                ->execute([$_SESSION['user_id'], $product_id, $quantity]);
        }
    }
    unset($_SESSION['cart']);
}

function is_safe_redirect($url) {
    if (empty($url)) return false;
    if (preg_match('#^(https?:)?//#i', $url)) return false;
    if (preg_match('#[:\\\\]#', $url)) return false;
    return true;
}

if ($user['is_admin'] == 1) {
    header('Location: /online-computer-store/admin.php');
    exit;
}

$candidate = '';

if (!empty($_SESSION['intended_redirect'])) {
    $candidate = $_SESSION['intended_redirect'];
    unset($_SESSION['intended_redirect']);
}

if (empty($candidate)) {
    if (!empty($_POST['redirect'])) {
        $candidate = $_POST['redirect'];
    } elseif (!empty($_GET['redirect'])) {
        $candidate = $_GET['redirect'];
    }
}

if ($candidate && is_safe_redirect($candidate)) {
    if ($candidate[0] !== '/') {
        $candidate = '/' . ltrim($candidate, '/');
    }
    header('Location: ' . $candidate);
    exit;
}

header('Location: /online-computer-store/index.php');
exit;