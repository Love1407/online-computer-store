<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Hmart - Auth</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="logo">Hmart</div>

   <div class="icons">
    <span>🛒</span>

    <?php if(isset($_SESSION['user'])): ?>

        <li><a href="/online-computer-store/account.php">My Account</a></li>
        <li><a href="/online-computer-store/logout.php">Logout</a></li>

    <?php else: ?>

        <li><a href="/online-computer-store/login.php">Login</a></li>
        <li><a href="/online-computer-store/signup.php">Sign Up</a></li>

    <?php endif; ?>
</div>

</header>

<nav class="nav-bar">
    <ul>
        <li><a href="/index.php">Home</a></li>
       <li><a href="/">About</a></li>
<li>Explore Categories</li>        
        <li>Explore Products</li>
    </ul>
</nav>

<main class="container">