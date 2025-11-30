<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['user_id'])){
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$cart_items = [];
$total = 0.0;

$stmt = $pdo->prepare("SELECT ci.quantity, p.id as product_id, p.product_name AS name, p.original_price, p.image_url 
                       FROM cart_items ci 
                       JOIN products p ON ci.product_id = p.id 
                       WHERE ci.user_id=?");


$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($cart_items as $item){
    $total += $item['original_price'] * $item['quantity'];
}

$stmt = $pdo->prepare("SELECT email, name FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$countries = [
    'United States' => ['Indiana','California','Texas','New York'],
    'Canada' => ['Ontario','Quebec','British Columbia','Alberta']
];

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email      = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $apartment  = trim($_POST['apartment'] ?? '');
    $city       = trim($_POST['city'] ?? '');
    $state      = trim($_POST['state'] ?? '');
    $country    = trim($_POST['country'] ?? '');
    $zip        = trim($_POST['zip'] ?? '');
    $card       = trim($_POST['card_number'] ?? '');
    $exp        = trim($_POST['exp_date'] ?? '');
    $cvv        = trim($_POST['cvv'] ?? '');
    $name_on_card = trim($_POST['name_on_card'] ?? '');

    if(!$first_name) $errors[] = "First name required";
    if(!$last_name) $errors[] = "Last name required";
    if(!$address) $errors[] = "Address required";
    if(!$city) $errors[] = "City required";
    if(!$state) $errors[] = "State required";
    if(!$country) $errors[] = "Country required";
    if(!$zip) $errors[] = "ZIP required";
    if(!$card) $errors[] = "Card number required";
    if(!$exp) $errors[] = "Expiration date required";
    if(!$cvv) $errors[] = "CVV required";
    if(!$name_on_card) $errors[] = "Name on card required";

    if(empty($errors)){
        $stmt = $pdo->prepare("INSERT INTO orders 
            (user_id,email,first_name,last_name,address,apartment,city,state,country,zip,shipping_method,payment_method,card_number,total)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $_SESSION['user_id'],$email,$first_name,$last_name,$address,$apartment,$city,$state,$country,$zip,
            'Economy','card',$card,$total
        ]);
        $order_id = $pdo->lastInsertId();

        foreach($cart_items as $item){
            $stmt = $pdo->prepare("INSERT INTO order_history (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['original_price']]);

            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
        }

        $pdo->prepare("DELETE FROM cart_items WHERE user_id=?")->execute([$_SESSION['user_id']]);

        header("Location: order_success.php?order_id=$order_id");
        exit;
    }
}

?>

<link rel="stylesheet" href="assets/css/cart.css">
<div class="ckt-container">

    <div class="ckt-left">
        <h2 class="ckt-heading">Contact</h2>
        <div class="ckt-form-group">
            <label>Email</label>
            <input type="text" class="ckt-input" name="email" id="email" value="<?=htmlspecialchars($user['email'])?>">
        </div>

        <h2 class="ckt-heading">Delivery</h2>
        <form method="POST" id="checkoutForm">

        <div class="ckt-2col">
            <div class="ckt-form-group">
                <label>Country/Region</label>
                <select class="ckt-input" name="country" id="country">
                    <option value="">Select country</option>
                    <?php foreach($countries as $c => $states): ?>
                        <option value="<?=$c?>"><?=$c?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="ckt-2col">
            <div class="ckt-form-group">
                <label>First name</label>
                <input type="text" class="ckt-input" name="first_name" id="first_name">
            </div>
            <div class="ckt-form-group">
                <label>Last name</label>
                <input type="text" class="ckt-input" name="last_name" id="last_name">
            </div>
        </div>

        <div class="ckt-form-group">
            <label>Address</label>
            <input type="text" class="ckt-input" name="address" id="address">
        </div>

        <div class="ckt-form-group">
            <label>Apartment, suite, etc. (optional)</label>
            <input type="text" class="ckt-input" name="apartment" id="apartment">
        </div>

        <div class="ckt-3col">
            <div class="ckt-form-group">
                <label>City</label>
                <input type="text" class="ckt-input" name="city" id="city">
            </div>
            <div class="ckt-form-group">
                <label>State</label>
                <select class="ckt-input" name="state" id="state">
                    <option value="">Select state</option>
                </select>
            </div>
            <div class="ckt-form-group">
                <label>ZIP code</label>
                <input type="text" class="ckt-input" name="zip" id="zip">
            </div>
        </div>

        <h2 class="ckt-heading">Payment</h2>
        <div class="ckt-form-group">
            <label>Card Number</label>
            <input type="text" class="ckt-input" name="card_number">
        </div>
        <div class="ckt-2col">
            <div class="ckt-form-group">
                <label>Expiration date (MM / YY)</label>
                <input type="text" class="ckt-input" name="exp_date">
            </div>
            <div class="ckt-form-group">
                <label>Security code</label>
                <input type="text" class="ckt-input" name="cvv">
            </div>
        </div>
        <div class="ckt-form-group">
            <label>Name on card</label>
            <input type="text" class="ckt-input" name="name_on_card">
        </div>

        <div>
            <?php if(!empty($errors)): ?>
                <div style="color:red;">
                    <?php foreach($errors as $e) echo "<div>$e</div>"; ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="ckt-pay-btn">Pay now</button>
        </form>
    </div>

    <div class="ckt-right">
      <?php foreach($cart_items as $item): ?>
<div class="ckt-summary-item">
    <?php
        $img = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/60';
    ?>
    <img src="<?= $img ?>" class="ckt-img" alt="<?= htmlspecialchars($item['name']) ?>">
    <div>
        <div class="ckt-prod-title"><?=htmlspecialchars($item['name'])?></div>
        <div>$<?=number_format($item['original_price'],2)?></div>
        <div>Qty: <?=$item['quantity']?></div>
    </div>
</div>
<?php endforeach; ?>
        <div class="ckt-summary-box">
            <div class="ckt-row"><span>Subtotal</span><strong>$<?=number_format($total,2)?></strong></div>
            <div class="ckt-row"><span>Shipping</span><strong>FREE</strong></div>
            <div class="ckt-total"><span>Total</span><strong>$<?=number_format($total,2)?></strong></div>
        </div>
    </div>
</div>

<script>
const countries = <?=json_encode($countries)?>;
const countrySelect = document.getElementById('country');
const stateSelect = document.getElementById('state');

countrySelect.addEventListener('change', function(){
    const states = countries[this.value] || [];
    stateSelect.innerHTML = '<option value="">Select state</option>';
    states.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s;
        opt.textContent = s;
        stateSelect.appendChild(opt);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>