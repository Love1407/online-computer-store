<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$search = trim($_GET['search'] ?? '');

try {
    $sql = "SELECT o.id, o.user_id, o.total, o.created_at, u.name AS user_name, u.email AS user_email
            FROM orders o
            JOIN users u ON o.user_id = u.id";
    $params = [];

    if ($search !== '') {
        $sql .= " WHERE o.id LIKE ? OR u.name LIKE ? OR u.email LIKE ?";
        $searchParam = "%$search%";
        $params = [$searchParam, $searchParam, $searchParam];
    }

    $sql .= " ORDER BY o.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="content" id="content">
    <div class="wrap">

        <h2>Order History</h2>

        <form method="get" style="margin-bottom:15px;">
            <input type="text" name="search" placeholder="Search by order ID, user name, or email" 
                   value="<?= htmlspecialchars($search) ?>" style="width:300px; padding:6px;">
            <button type="submit">Search</button>
            <?php if($search !== ''): ?>
                <a href="admin_order_history.php"><button type="button">Reset</button></a>
            <?php endif; ?>
        </form>

        <table style="width:100%; border-collapse: collapse;">
            <tr style="background:#eee;">
                <th>Order ID</th>
                <th>User</th>
                <th>Email</th>
                <th>Total ($)</th>
                <th>Placed On</th>
                <th>Action</th>
            </tr>

            <?php if(count($orders) > 0): ?>
                <?php foreach($orders as $order): ?>
                <tr class="order-row" data-order-id="<?= $order['id'] ?>">
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                    <td><?= htmlspecialchars($order['user_email']) ?></td>
                    <td><?= number_format($order['total'],2) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td><button type="button" class="toggle-items-btn" data-order-id="<?= $order['id'] ?>">View</button></td>
                </tr>
                <tr class="order-items" id="order-items-<?= $order['id'] ?>" style="display:none; background:#f9f9f9;">
                    <td colspan="6">
                        <strong>Items:</strong>
                        <table style="width:100%; margin-top:5px; border-collapse: collapse;">
                            <tr style="background:#ddd;">
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Price ($)</th>
                                <th>Subtotal ($)</th>
                            </tr>
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
                                <td><?= number_format($item['price'],2) ?></td>
                                <td><?= number_format($itemSubtotal,2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr style="background:#eee; font-weight:bold;">
                                <td colspan="3" style="text-align:right;">Order Subtotal:</td>
                                <td><?= number_format($orderSubtotal,2) ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No orders found.</td>
                </tr>
            <?php endif; ?>
        </table>

    </div>
</div>

<script>
document.querySelectorAll('.toggle-items-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;
        const itemsRow = document.getElementById('order-items-' + orderId);
        if (itemsRow.style.display === 'table-row') {
            itemsRow.style.display = 'none';
            btn.textContent = 'View';
        } else {
            itemsRow.style.display = 'table-row';
            btn.textContent = 'Hide';
        }
    });
});
</script>

</body>
</html>