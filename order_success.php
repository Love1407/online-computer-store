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

<style>

    /* Success Animation Container */
    .success-animation {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeOut 0.5s ease-in-out 2.5s forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            visibility: hidden;
        }
    }
    

    /* Checkmark Circle */
    /* .checkmark-circle {
        width: 120px;
        height: 120px;
        position: relative;
        display: inline-block;
        vertical-align: top;
        margin-bottom: 30px;
    } */

    .checkmark-circle .background {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #10b981;
        position: absolute;
        /* animation: scaleIn 0.5s ease -in-out; */
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    /* .checkmark-circle .checkmark {
        border-radius: 5px;
    }

    .checkmark-circle .checkmark.draw:after {
        animation: checkmark 0.8s ease 0.5s forwards;
    }

    .checkmark-circle .checkmark:after {
        opacity: 1;
        height: 60px;
        width: 30px;
        transform-origin: left top;
        border-right: 5px solid white;
        border-top: 5px solid white;
        border-radius: 3px;
        content: '';
        left: 28px;
        top: 60px;
        position: absolute;
        transform: scaleX(-1) rotate(135deg);
        transform: rotate(45deg);
    } */
    .checkmark-circle {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: #4CAF50;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkmark {
    position: relative;
    width: 70px;
    height: 70px;
}

.checkmark:after {
    content: '';
    position: absolute;
    width: 25px;        /* right stroke */
    height: 50px;       /* left-down stroke */
    border-right: 5px solid white;
    border-bottom: 5px solid white;
    transform: rotate(45deg);
    top: 5px;           /* adjust vertical */
    left: 18px;         /* adjust horizontal */
}


    @keyframes checkmark {
        0% {
            height: 0;
            width: 0;
            opacity: 1;
        }
        20% {
            height: 0;
            width: 30px;
            opacity: 1;
        }
        40% {
            height: 60px;
            width: 30px;
            opacity: 1;
        }
        100% {
            height: 60px;
            width: 30px;
            opacity: 1;
        }
    }

    .success-text {
        font-size: 32px;
        font-weight: 700;
        color: #10b981;
        margin-bottom: 10px;
        animation: slideUp 0.6s ease-in-out 0.8s both;
    }

    .success-subtext {
        font-size: 18px;
        color: #6b7280;
        animation: slideUp 0.6s ease-in-out 1s both;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Main Content */
    .ckt-container {
        max-width: 1200px;
        margin: 40px auto;
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
        opacity: 0;
        animation: fadeIn 0.8s ease-in-out 3s forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }

    .ckt-left, .ckt-right {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .ckt-heading {
        font-size: 28px;
        color: #1f2937;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ckt-heading::before {
        content: '✓';
        width: 32px;
        height: 32px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
    }

    .ckt-left > p {
        color: #6b7280;
        font-size: 16px;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f3f4f6;
    }

    h3 {
        font-size: 20px;
        color: #1f2937;
        margin-bottom: 15px;
        margin-top: 30px;
    }

    .ckt-left h3:first-of-type {
        margin-top: 0;
    }

    .ckt-left p {
        color: #4b5563;
        line-height: 1.8;
        font-size: 15px;
    }

    /* Order Summary */
    .ckt-right h3 {
        margin-top: 0;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .ckt-summary-item {
        display: flex;
        gap: 15px;
        padding: 20px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .ckt-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .ckt-prod-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .ckt-summary-item > div > div {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .ckt-summary-box {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid #f3f4f6;
    }

    .ckt-row, .ckt-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        font-size: 15px;
    }

    .ckt-row span {
        color: #6b7280;
    }

    .ckt-row strong {
        color: #1f2937;
        font-weight: 600;
    }

    .ckt-total {
        border-top: 2px solid #f3f4f6;
        margin-top: 10px;
        padding-top: 20px;
        font-size: 20px;
        font-weight: 700;
    }

    .ckt-total span {
        color: #1f2937;
    }

    .ckt-total strong {
        color: #10b981;
    }

    @media (max-width: 968px) {
        .ckt-container {
            grid-template-columns: 1fr;
        }

        .ckt-left, .ckt-right {
            padding: 30px 20px;
        }

        .success-text {
            font-size: 24px;
        }

        .checkmark-circle {
            width: 100px;
            height: 100px;
        }

        .checkmark-circle .background {
            width: 100px;
            height: 100px;
        }

        .checkmark-circle .checkmark:after {
            height: 50px;
            width: 25px;
            left: 24px;
            top: 50px;
        }
    }
</style>

<!-- Success Animation -->
<div class="success-animation">
    <div class="checkmark-circle">
        <div class="background"></div>
        <div class="checkmark draw"></div>
    </div>
    <div class="success-text">Payment Completed!</div>
    <div class="success-subtext">Thank you for your order</div>
</div>

<!-- Order Details -->
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