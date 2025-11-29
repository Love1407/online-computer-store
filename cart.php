<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$items = [];
$total = 0;

if (isset($_SESSION['user_id'])) {

    $stmt = $pdo->prepare("
        SELECT c.id, c.quantity, p.product_name, p.original_price, p.deal_price, p.image_url
        FROM cart_items c
        INNER JOIN products p ON c.product_id = p.id
        WHERE c.user_id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll();

} else {

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $ci) {
            $pid = $ci['product_id'];

            $stmt = $pdo->prepare("SELECT product_name, original_price, deal_price, image_url FROM products WHERE id=?");
            $stmt->execute([$pid]);
            $p = $stmt->fetch();

            if ($p) {
                $items[] = [
                    "id" => $pid,
                    "quantity" => $ci['quantity'],
                    "product_name" => $p['product_name'],
                    "original_price" => $p['original_price'],
                    "deal_price" => $p['deal_price'],
                    "image_url" => $p['image_url']
                ];
            }
        }
    }
}
?>

<h2>Your Cart</h2>

<table class="cart-table">
<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Subtotal</th>
    <th>Remove</th>
</tr>

<?php foreach($items as $i): ?>
<?php 
$price = $i['deal_price'] ?: $i['original_price'];
$subtotal = $price * $i['quantity'];
$total += $subtotal;
?>
<tr>
    <td><img src="<?= 'uploads/' . $i['image_url'] ?>" width="60"></td>
    <td><?= htmlspecialchars($i['product_name']) ?></td>
    <td><?= $i['quantity'] ?></td>
    <td>₹<?= $price ?></td>
    <td>₹<?= $subtotal ?></td>
    <td>
        <a href="remove_cart_item.php?id=<?= $i['id'] ?>">Remove</a>
    </td>
</tr>
<?php endforeach; ?>

</table>

<h3>Total: ₹<?= $total ?></h3>

<?php if ($total > 0): ?>
<button class="checkout-btn">Proceed to Checkout</button>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>