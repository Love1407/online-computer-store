<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$order_id = $_GET['order_id'] ?? null;
if(!$order_id){
    echo "<h2>No order found.</h2>";
    exit;
}
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();
if(!$order){
    echo "<h2>Order not found.</h2>";
    exit;
}

$stmt = $pdo->prepare("SELECT oh.quantity, oh.price AS original_price, p.product_name, p.image_url 
                       FROM order_history oh 
                       JOIN products p ON oh.product_id = p.id 
                       WHERE oh.order_id=?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach($order_items as $item) $subtotal += $item['original_price'] * $item['quantity'];
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div class="ckt-container">
    <div class="ckt-left">
        <h2 class="ckt-heading">Thank you for your order!</h2>
        <p>Order #<?=htmlspecialchars($order['id'])?> placed on <?=date('F j, Y', strtotime($order['created_at']))?></p>

        <h3>Shipping Information</h3>
        <p>
            <?=htmlspecialchars($order['first_name'].' '.$order['last_name'])?><br>
            <?=htmlspecialchars($order['address'])?><br>
            <?php if($order['apartment']): ?><?=htmlspecialchars($order['apartment'])?><br><?php endif; ?>
            <?=htmlspecialchars($order['city'].', '.$order['state'].' '.$order['zip'])?><br>
            <?=htmlspecialchars($order['country'])?><br>
            Email: <?=htmlspecialchars($order['email'])?>
        </p>

        <h3>Payment Method</h3>
        <p>Card ending with <?=substr($order['card_number'],-4)?></p>
    </div>

    <div class="ckt-right">
        <h3>Order Summary</h3>
        <?php foreach($order_items as $item): ?>
        <div class="ckt-summary-item">
            <?php
                $img = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/60';
            ?>
            <img src="<?= $img ?>" class="ckt-img" alt="<?= htmlspecialchars($item['product_name']) ?>">
            <div>
                <div class="ckt-prod-title"><?=htmlspecialchars($item['product_name'])?></div>
                <div>Qty: <?=$item['quantity']?></div>
                <div>$<?=number_format($item['original_price'], 2)?></div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="ckt-summary-box">
            <div class="ckt-row"><span>Subtotal</span><strong>$<?=number_format($subtotal,2)?></strong></div>
            <div class="ckt-total"><span>Total</span><strong>$<?=number_format($order['total'],2)?></strong></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>