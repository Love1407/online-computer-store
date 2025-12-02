<?php
require_once __DIR__ . '/includes/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
header('Location: signup.php'); exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$errors = [];
if($name === '') $errors[] = 'Name is required';
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if(strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters';
if(!preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) $errors[] = 'Password must contain letters and numbers';
if($pass !== $confirm) $errors[] = 'Passwords do not match';
if($errors){
$msg = urlencode(implode('. ', $errors));
header('Location: signup.php?error=' . $msg);
exit;
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if($stmt->fetch()){
header('Location: signup.php?error=' . urlencode('Email already registered'));
exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$insert = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
$insert->execute([$name, $email, $hash]);

header('Location: signup.php?success=' . urlencode('Account created. Please login.'));
exit;