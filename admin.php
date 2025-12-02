<?php require_once __DIR__ . '/includes/sidebar.php'; ?>
<?php require_once __DIR__ . '/includes/db.php'; 
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_orders FROM orders");
$stmt->execute();
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) AS total_products FROM products");
$stmt->execute();
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="assets/css/order.css">

<div class="adm-content" id="content">
    <div class="adm-welcome">
        <h1 class="adm-welcome-title">Welcome back, Admin</h1>
        <p class="adm-welcome-subtitle">Here's what's happening with your store today</p>
    </div>

    <div class="adm-stats-grid">

     <div class="adm-stat-card adm-warning">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Total Orders</div>
                </div>
            </div>
            <div class="adm-stat-value"><?= $total_orders ?></div>
        </div>


        <div class="adm-stat-card adm-warning">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Products</div>
                </div>
            </div>
            <div class="adm-stat-value"><?= $total_products ?></div>
        </div>

        <div class="adm-stat-card adm-info">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Active Users</div>
                </div>
            </div>
            <div class="adm-stat-value"><?= $total_users ?></div>  
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">Quick Actions</h3>
        </div>
        <div class="adm-card-actions">
            <a href="/online-computer-store/products.php" class="adm-btn adm-btn-primary">
                Add New Product
            </a>
            <a href="/online-computer-store/admincategories.php" class="adm-btn adm-btn-success">
                Manage Categories
            </a>
            <a href="/online-computer-store/admin_order_history.php" class="adm-btn adm-btn-secondary">
                View Orders
            </a>
            <a href="/online-computer-store/userdetails.php" class="adm-btn adm-btn-secondary">
                Manage Users
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">Recent Orders</h3>
            <a href="/online-computer-store/admin_order_history.php" class="adm-btn adm-btn-sm adm-btn-secondary">
                View All Orders
            </a>
        </div>
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
</body>
</html>