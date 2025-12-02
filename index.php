<?php require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';
 ?>

<?php
$stmt = $pdo->prepare("
    SELECT id, product_name, description, original_price, deal_price, image_url, is_on_sale, created_at
    FROM products
    WHERE is_on_sale = 1
    ORDER BY created_at DESC
    LIMIT 8
");

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [
    [
        'id' => 1,
        'name' => 'Laptops',
        'image_alt' => 'Laptop category image',
        'image_src' => 'assets/images/groups/laptops.png'
    ],
    [
        'id' => 2,
        'name' => 'Desktop Computers',
        'image_alt' => 'Desktop Computer category image',
        'image_src' => 'assets/images/groups/desktops.png'
    ],
    [
        'id' => 3,
        'name' => 'PC Components',
        'image_alt' => 'PC Components category image',
        'image_src' => 'assets/images/groups/components.png'
    ],
    [
        'id' => 4,
        'name' => 'Displays & Monitors',
        'image_alt' => 'Monitor category image',
        'image_src' => 'assets/images/groups/displays.png'
    ],
    [
        'id' => 5,
        'name' => 'Peripherals & Accessories',
        'image_alt' => 'Peripheral category image',
        'image_src' => 'assets/images/groups/accessories.png'
    ],
    [
        'id' => 6,
        'name' => 'Cables & Adapters',
        'image_alt' => 'Cable and Adapter category image',
        'image_src' => 'assets/images/groups/cables.png'
    ],
];

?>

<section class="slider">
    <button class="arrow left" onclick="prevSlide()">❮</button>
    <div class="slides">
        <div class="slide active">
            <div class="slide-content">
                <img src="assets/images/banner/1.png" class="slide-img">
                <div class="text-box">
                    <h1>Easy Your Life<br>With Smart Device</h1>
                    <a href="/online-computer-store/exploreproducts.php" style="text-decoration:none;">
                        <button class="shop-btn">Shop All Devices</button>
</a>
                </div>
            </div>
        </div>

        <div class="slide">
            <div class="slide-content">
                <img src="assets/images/banner/2.png" class="slide-img">
                <div class="text-box">
                    <h1>Upgrade Your Gadgets</h1>
                      <a href="/online-computer-store/exploreproducts.php" style="text-decoration:none;">
                    <button class="shop-btn">Shop Now</button>
</a>
                </div>
            </div>
        </div>

        <div class="slide">
            <div class="slide-content">
                <img src="assets/images/banner/3.png" class="slide-img">
                <div class="text-box">
                    <h1>Latest Smart Accessories</h1>
                      <a href="/online-computer-store/exploreproducts.php" style="text-decoration:none;">
                    <button class="shop-btn">Explore</button>
</a>
                </div>
            </div>
        </div>

    </div>

    <button class="arrow right" onclick="nextSlide()">❯</button>
</section>

<section class="category-box">
    
    <?php
    foreach ($categories as $category) {
        $target_url = 'exploreproducts.php?group=' . urlencode($category['id']);
    ?>
    
    <a href="<?= htmlspecialchars($target_url) ?>" class="cat-card-link">
        <div class="cat-card" data-category-id="<?php echo htmlspecialchars($category['id']); ?>">
            <img 
                src="<?php echo htmlspecialchars($category['image_src']); ?>" 
                alt="<?php echo htmlspecialchars($category['image_alt']); ?>" 
                class="cat-image"
            >
            
            <h2><?php echo htmlspecialchars($category['name']); ?></h2>
            <button class="blue-btn" onclick="event.preventDefault(); window.location.href='<?= htmlspecialchars($target_url) ?>';">Shop Now</button>
        </div>
    </a>
    
    <?php
    }
    ?>

</section>

<section class="products">
    <h2>New Products</h2>
    <p class="sub-text">Choose from the best trending items</p>

    <div class="product-grid">

        <?php  
        foreach ($products as $p) {
            $tag = "SALE";
            $img = $p['image_url'] ?: 'assets/images/noimg.png';

            echo "
                <div class='product'>
                    <span class='badge'>{$tag}</span>
                    <img src='{$img}' alt=''>
                    <h4>{$p['product_name']}</h4>
                    <p class='price'>$" . number_format(($p['deal_price'] ?? $p['original_price']), 2) . "</p>
                </div>
            ";
        }
        ?>

    </div>
</section>

<section class="red-banner">
    <div class="red-content">
        <h2>Smart World</h2>
        <h1>With Smart Devices</h1>
       <a href="/online-computer-store/exploreproducts.php" style="text-decoration:none;">
    <button class="white-btn">Shop All Devices</button>
</a>
    </div>
</section>

<section class="services">
    <div class="service-box">
        <i class="fas fa-truck-fast service-icon" aria-hidden="true"></i>
        <h3>Free Shipping</h3>
    </div>

    <div class="service-box">
        <i class="fas fa-credit-card service-icon" aria-hidden="true"></i>
        <h3>Card Payments</h3>
    </div>

    <div class="service-box">
        <i class="fas fa-rotate-left service-icon" aria-hidden="true"></i>
        <h3>Easy Returns</h3>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/index.js"></script>