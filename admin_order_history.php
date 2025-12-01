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

<div class="adm-content" id="content">
    <div class="adm-page-header">
        <h1 class="adm-page-title">Order History</h1>
        <div class="adm-breadcrumb">
            <a href="admin.php">Dashboard</a>
            <span>/</span>
            <span>Order History</span>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">Search Orders</h3>
        </div>
        <form method="get" class="adm-search-form">
            <div class="adm-search-wrapper">
                <input 
                    type="text" 
                    name="search" 
                    class="adm-search-field" 
                    placeholder="🔍 Search by order ID, customer name, or email..." 
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="adm-btn adm-btn-primary">Search</button>
                <?php if($search !== ''): ?>
                    <a href="admin_order_history.php" class="adm-btn adm-btn-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">
                All Orders 
                <span class="adm-count-badge"><?= count($orders) ?></span>
            </h3>
            <?php if($search !== ''): ?>
                <span class="adm-search-indicator">
                    Showing results for: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                </span>
            <?php endif; ?>
        </div>

        <?php if(count($orders) > 0): ?>
            <div class="adm-table-wrapper">
                <table class="adm-table adm-orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr class="adm-order-row" data-order-id="<?= $order['id'] ?>">
                            <td>
                                <span class="adm-order-id">#ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            <td>
                                <div class="adm-customer-info">
                                    <strong><?= htmlspecialchars($order['user_name']) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($order['user_email']) ?></td>
                            <td>
                                <span class="adm-amount">$<?= number_format($order['total'], 2) ?></span>
                            </td>
                            <td>
                                <span class="adm-date"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                                <br>
                                <small class="adm-time"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                            </td>
                            <td>
                                <button 
                                    type="button" 
                                    class="adm-btn adm-btn-sm adm-btn-primary adm-toggle-items-btn" 
                                    data-order-id="<?= $order['id'] ?>">
                                    👁️ View Items
                                </button>
                            </td>
                        </tr>

                        <tr class="adm-order-items-row" id="order-items-<?= $order['id'] ?>" style="display:none;">
                            <td colspan="6" class="adm-items-cell">
                                <div class="adm-items-container">
                                    <h4 class="adm-items-title">📦 Order Items</h4>
                                    <table class="adm-items-table">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Subtotal</th>
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
                                                <td>
                                                    <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="adm-qty-badge"><?= $item['quantity'] ?>x</span>
                                                </td>
                                                <td>$<?= number_format($item['price'], 2) ?></td>
                                                <td><strong>$<?= number_format($itemSubtotal, 2) ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="adm-items-total">
                                                <td colspan="3"><strong>Order Subtotal:</strong></td>
                                                <td><strong class="adm-total-amount">$<?= number_format($orderSubtotal, 2) ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="adm-empty-state">
                <div class="adm-empty-icon">📋</div>
                <h3 class="adm-empty-title">No orders found</h3>
                <p class="adm-empty-text">
                    <?php if($search !== ''): ?>
                        No orders match your search criteria. Try a different search term.
                    <?php else: ?>
                        There are no orders in the system yet.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.adm-search-form {
    margin: 0;
}

.adm-search-wrapper {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.adm-search-field {
    flex: 1;
    min-width: 300px;
    padding: 0.875rem 1.25rem;
    border: 2px solid var(--adm-gray-light);
    border-radius: var(--adm-radius);
    font-size: 1rem;
    transition: var(--adm-transition);
    background: var(--adm-gray-lighter);
}

.adm-search-field:focus {
    outline: none;
    border-color: var(--adm-primary);
    background: var(--adm-white);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.adm-count-badge {
    background: linear-gradient(135deg, var(--adm-primary), var(--adm-primary-light));
    color: var(--adm-white);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    margin-left: 0.5rem;
}

.adm-search-indicator {
    color: var(--adm-gray);
    font-size: 0.9rem;
}

.adm-search-indicator strong {
    color: var(--adm-primary);
}

.adm-orders-table tbody tr.adm-order-row {
    cursor: pointer;
    transition: var(--adm-transition);
}

.adm-orders-table tbody tr.adm-order-row:hover {
    background: rgba(37, 99, 235, 0.02);
}

.adm-order-id {
    font-weight: 800;
    color: var(--adm-primary);
    font-size: 0.95rem;
}

.adm-customer-info strong {
    color: var(--adm-dark);
    font-weight: 700;
}

.adm-amount {
    font-weight: 900;
    color: var(--adm-secondary);
    font-size: 1.1rem;
}

.adm-date {
    color: var(--adm-gray-dark);
    font-weight: 600;
}

.adm-time {
    color: var(--adm-gray);
    font-size: 0.8rem;
}

.adm-order-items-row {
    background: var(--adm-gray-lighter) !important;
}

.adm-items-cell {
    padding: 0 !important;
}

.adm-items-container {
    padding: 2rem;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.03), rgba(255, 255, 255, 1));
    border-left: 4px solid var(--adm-primary);
}

.adm-items-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--adm-dark);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.adm-items-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--adm-white);
    border-radius: var(--adm-radius);
    overflow: hidden;
    box-shadow: var(--adm-shadow);
}

.adm-items-table thead {
    background: linear-gradient(135deg, var(--adm-primary), var(--adm-primary-light));
    color: var(--adm-white);
}

.adm-items-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.adm-items-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--adm-gray-lighter);
}

.adm-items-table tbody tr:last-child td {
    border-bottom: none;
}

.adm-items-table tbody tr:hover {
    background: var(--adm-gray-lighter);
}

.adm-qty-badge {
    background: rgba(37, 99, 235, 0.1);
    color: var(--adm-primary);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
}

.adm-items-total {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(59, 130, 246, 0.05));
}

.adm-items-total td {
    padding: 1.25rem 1rem !important;
    font-size: 1.1rem;
}

.adm-total-amount {
    color: var(--adm-primary);
    font-size: 1.25rem;
}

.adm-empty-state {
    text-align: center;
    padding: 5rem 2rem;
}

.adm-empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.adm-empty-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--adm-dark);
    margin-bottom: 0.5rem;
}

.adm-empty-text {
    color: var(--adm-gray);
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .adm-search-wrapper {
        flex-direction: column;
    }

    .adm-search-field {
        width: 100%;
    }

    .adm-btn {
        width: 100%;
    }

    .adm-items-container {
        padding: 1rem;
    }

    .adm-items-table {
        font-size: 0.85rem;
    }

    .adm-items-table th,
    .adm-items-table td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<script>
document.querySelectorAll('.adm-toggle-items-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const itemsRow = document.getElementById('order-items-' + orderId);
        
        if (itemsRow.style.display === 'table-row') {
            itemsRow.style.display = 'none';
            this.innerHTML = '👁️ View Items';
            this.classList.remove('adm-btn-secondary');
            this.classList.add('adm-btn-primary');
        } else {
            document.querySelectorAll('.adm-order-items-row').forEach(row => {
                row.style.display = 'none';
            });
            document.querySelectorAll('.adm-toggle-items-btn').forEach(otherBtn => {
                otherBtn.innerHTML = '👁️ View Items';
                otherBtn.classList.remove('adm-btn-secondary');
                otherBtn.classList.add('adm-btn-primary');
            });
            itemsRow.style.display = 'table-row';
            this.innerHTML = '🔽 Hide Items';
            this.classList.remove('adm-btn-primary');
            this.classList.add('adm-btn-secondary');
        }
    });
});

document.querySelectorAll('.adm-order-row').forEach(row => {
    row.addEventListener('click', function(e) {
        if (e.target.closest('.adm-toggle-items-btn')) return;
        
        const orderId = this.dataset.orderId;
        const btn = document.querySelector(`.adm-toggle-items-btn[data-order-id="${orderId}"]`);
        if (btn) btn.click();
    });
});
</script>

</body>
</html>