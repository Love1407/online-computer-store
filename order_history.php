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

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.ckt-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    gap: 20px;
}

.ckt-left {
    flex: 1;
    background: #ffffff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.ckt-right {
    width: 300px;
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.ckt-heading {
    font-size: 32px;
    color: #333;
    margin-bottom: 30px;
    font-weight: 700;
    border-bottom: 3px solid #667eea;
    padding-bottom: 15px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
    font-size: 18px;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.orders-table thead tr {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
}

.orders-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.orders-table th:first-child {
    border-radius: 8px 0 0 0;
}

.orders-table th:last-child {
    border-radius: 0 8px 0 0;
}

.order-row {
    border-bottom: 1px solid #e0e0e0;
    transition: background-color 0.3s ease;
}

.order-row:hover {
    background-color: #f5f5f5;
}

.order-row td {
    padding: 18px 15px;
    color: #333;
    font-size: 15px;
}

.order-items {
    background: #fafafa;
    border-bottom: 2px solid #667eea;
}

.order-items td {
    padding: 20px 15px;
}

.items-label {
    font-weight: 700;
    color: #667eea;
    font-size: 16px;
    margin-bottom: 15px;
    display: block;
}

.items-table {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.items-table thead tr {
    background: #f0f0f0;
    color: #333;
}

.items-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    border-bottom: 2px solid #667eea;
}

.items-table td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    color: #555;
}

.items-table tr:last-child td {
    border-bottom: none;
}

.subtotal-row {
    background: #f9f9f9;
    font-weight: 700;
    color: #333;
}

.subtotal-row td {
    padding: 15px 12px;
    font-size: 16px;
}

.toggle-items-btn {
    padding: 8px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.toggle-items-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.toggle-items-btn:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .ckt-container {
        flex-direction: column;
    }
    
    .orders-table {
        font-size: 13px;
    }
    
    .orders-table th,
    .orders-table td {
        padding: 10px 8px;
    }
    
    .ckt-heading {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 10px;
    }
    
    .ckt-left {
        padding: 15px;
    }
    
    .orders-table,
    .items-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}
</style>

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