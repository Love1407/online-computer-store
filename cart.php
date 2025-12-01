<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$items = [];
$total = 0;

if (isset($_GET['update_qty']) && isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']); 
    $action = $_GET['action'];

    if (isset($_SESSION['user_id'])) {
        if (!isset($pdo)) {
            header("Location: cart.php");
            exit;
        }

        $q = $pdo->prepare("SELECT id, quantity, user_id FROM cart_items WHERE id = ?");
        $q->execute([$id]);
        $row = $q->fetch(PDO::FETCH_ASSOC);

        if ($row && intval($row['user_id']) === intval($_SESSION['user_id'])) {
            $qty = intval($row['quantity']);
            if ($action === 'plus') {
                $qty++;
            } elseif ($action === 'minus' && $qty > 1) {
                $qty--;
            }

            $u = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $u->execute([$qty, $id]);
        }
    } else {
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $k => $ci) {
                if (isset($ci['product_id']) && intval($ci['product_id']) === $id) {
                    $qty = intval($ci['quantity']);
                    if ($action === 'plus') {
                        $qty++;
                    } elseif ($action === 'minus' && $qty > 1) {
                        $qty--;
                    }
                    $_SESSION['cart'][$k]['quantity'] = $qty;
                    break;
                }
            }
        }
    }
    header("Location: cart.php");
    exit;
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT 
            c.id AS cart_id,
            c.quantity,
            p.product_name,
            p.original_price,
            p.deal_price,
            p.image_url
        FROM cart_items c
        INNER JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.id DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $ci) {
            $pid = intval($ci['product_id']);

            $stmt = $pdo->prepare("
                SELECT product_name, original_price, deal_price, image_url
                FROM products
                WHERE id = ?
            ");
            $stmt->execute([$pid]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($p) {
                $items[] = [
                    "cart_id"       => $pid,
                    "quantity"      => intval($ci['quantity']),
                    "product_name"  => $p['product_name'],
                    "original_price"=> $p['original_price'],
                    "deal_price"    => $p['deal_price'],
                    "image_url"     => $p['image_url']
                ];
            }
        }
    }
}

foreach ($items as $i) {
    $price = $i['deal_price'] ?: $i['original_price'];
    $total += $price * $i['quantity'];
}
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div class="prt-cart-wrapper">
    <div class="prt-cart-container">
        
        <?php if (empty($items)): ?>
            <div class="prt-empty-cart">
                <div class="prt-empty-icon">🛒</div>
                <h2 class="prt-empty-text">Your cart is empty</h2>
                <p class="prt-empty-subtext">Add some products to get started!</p>
                <a href="exploreproducts.php" class="prt-continue-btn">Start Shopping</a>
            </div>
        <?php else: ?>
            
            <div class="prt-cart-header">
                <h2 class="prt-cart-heading">Shopping Cart</h2>
                <span class="prt-cart-count"><?= count($items) ?> Items</span>
            </div>

            <div class="prt-cart-content">
                <div class="prt-cart-items">
                    <?php foreach ($items as $i):
                        $price = $i['deal_price'] ?: $i['original_price'];
                        $subtotal = $price * $i['quantity'];
                    ?>
                    <div class="prt-cart-item">
                        <div class="prt-cart-img-wrapper">
                            <?php
                                $img = htmlspecialchars($i['image_url']);
                                if (empty($img)) $img = 'assets/images/placeholder.png';
                            ?>
                            <img src="<?= $img ?>" class="prt-cart-img" alt="<?= htmlspecialchars($i['product_name']) ?>">
                        </div>
                        
                        <div class="prt-cart-details">
                            <h3 class="prt-title"><span class="prt-title-text"><?= htmlspecialchars($i['product_name']) ?></span></h3>
                            <div class="prt-price-wrapper">
                                <span class="prt-price">$<?= number_format($price, 2) ?></span>
                                <?php if ($i['deal_price']): 
                                    $discount = round((($i['original_price'] - $i['deal_price']) / $i['original_price']) * 100);
                                ?>
                                    <span class="prt-original-price">$<?= number_format($i['original_price'], 2) ?></span>
                                    <span class="prt-discount-badge"><?= $discount ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="prt-cart-actions">
                            <div class="prt-cart-qty">
                                <a href="cart.php?update_qty=1&id=<?= $i['cart_id'] ?>&action=minus" class="prt-qty-btn">−</a>
                                <span class="prt-qty-number"><?= $i['quantity'] ?></span>
                                <a href="cart.php?update_qty=1&id=<?= $i['cart_id'] ?>&action=plus" class="prt-qty-btn">+</a>
                            </div>

                            <a class="prt-delete-btn" href="remove_cart_item.php?id=<?= $i['cart_id'] ?>" 
                               onclick="return confirm('Remove this item from cart?')" title="Remove item"></a>
                        </div>

                        <div class="prt-cart-right-price">
                            <div class="prt-subtotal-label">Subtotal</div>
                           $<?= number_format($subtotal, 2) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="prt-summary">
                    <h3 class="prt-summary-title">Order Summary</h3>
                    
                    <div class="prt-summary-row">
                        <span>Subtotal (<?= count($items) ?> items)</span>
                        <strong>$<?= number_format($total, 2) ?></strong>
                    </div>

                    <div class="prt-summary-row">
                        <span>Shipping</span>
                        <strong class="prt-summary-shipping">FREE</strong>
                    </div>

                    <div class="prt-summary-row">
                        <span>Tax (18%)</span>
                        <strong>$<?= number_format($total * 0.18, 2) ?></strong>
                    </div>

                    <div class="prt-summary-total">
                        <span>Total</span>
                        <strong>$<?= number_format($total * 1.18, 2) ?></strong>
                    </div>

                    <p class="prt-tax-note">All taxes and shipping included</p>

                    <button class="prt-checkout-btn" id="checkoutBtn">
                        Proceed to Checkout
                    </button>
                    
                    <a href="exploreproducts.php" class="prt-continue-shopping">Continue Shopping</a>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('checkoutBtn')?.addEventListener('click', function() {
    window.location.href = 'checkout_handler.php';
});

document.addEventListener("click", (e) => {
    if (e.target.classList.contains("prt-checkout-btn")) {
        let btn = e.target;
        let ripple = document.createElement("span");
        ripple.classList.add("ripple");

        let rect = btn.getBoundingClientRect();
        ripple.style.left = `${e.clientX - rect.left}px`;
        ripple.style.top = `${e.clientY - rect.top}px`;

        btn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
