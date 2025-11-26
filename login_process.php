<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if(!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === ''){
    header('Location: login.php?error=' . urlencode('Invalid credentials'));
    exit;
}

$stmt = $pdo->prepare('SELECT id, name, email, password, is_admin FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if(!$user || !password_verify($pass, $user['password'])){
    header('Location: login.php?error=' . urlencode('Invalid email or password'));
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['is_admin']  = $user['is_admin'];

if($user['is_admin'] == 1){
    header('Location: /online-computer-store/admin.php/');
    exit;
} else {
    header('Location: /online-computer-store/index.php');
    exit;
}
?>
