<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['user_id'])){
    header('Location: login.php?redirect=order_history.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/order.css">

<div class="ckt-container">
    <div class="ckt-left">
        <h2 class="ckt-heading">My Orders</h2>

        <?php if(empty($orders)): ?>
            <p class="empty-state">You have not placed any orders yet.</p>
        <?php else: ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Placed On</th>
                        <th>Total ($)</th>
                        <th>Shipping</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                    <tr class="order-row" data-order-id="<?= $order['id'] ?>">
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('F j, Y', strtotime($order['created_at'])) ?></td>
                        <td>$<?= number_format($order['total'],2) ?></td>
                        <td><?= htmlspecialchars($order['shipping_method']) ?></td>
                        <td><button type="button" class="toggle-items-btn" data-order-id="<?= $order['id'] ?>">View Details</button></td>
                    </tr>
                    <tr class="order-items" id="order-items-<?= $order['id'] ?>" style="display:none;">
                        <td colspan="5">
                            <span class="items-label">Order Items:</span>
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Price ($)</th>
                                        <th>Subtotal ($)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt2 = $pdo->prepare("SELECT oh.quantity, oh.price, p.product_name
                                                            FROM order_history oh
                                                            JOIN products p ON oh.product_id = p.id
                                                            WHERE oh.order_id=?");
                                    $stmt2->execute([$order['id']]);
                                    $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                                    $orderSubtotal = 0;
                                    foreach($items as $item):
                                        $itemSubtotal = $item['quantity'] * $item['price'];
                                        $orderSubtotal += $itemSubtotal;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['price'],2) ?></td>
                                        <td>$<?= number_format($itemSubtotal,2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="subtotal-row">
                                        <td colspan="3" style="text-align:right;">Order Subtotal:</td>
                                        <td>$<?= number_format($orderSubtotal,2) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-items-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;
        const itemsRow = document.getElementById('order-items-' + orderId);
        if (itemsRow.style.display === 'table-row') {
            itemsRow.style.display = 'none';
            btn.textContent = 'View Details';
        } else {
            itemsRow.style.display = 'table-row';
            btn.textContent = 'Hide Details';
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>