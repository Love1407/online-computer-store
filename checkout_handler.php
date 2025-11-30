<?php
session_start();

$currentRequest = 'online-computer-store/checkout.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_redirect'] = $currentRequest;
    header('Location: login.php');
    exit;
}

header('Location: checkout.php');
exit;
?>